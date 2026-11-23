import Chart from 'chart.js/auto';
import './calculator';
import { createIcons, icons } from 'lucide';
import './records';

window.Chart = Chart;

const rupiah = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);

document.addEventListener('DOMContentLoaded', () => {
    function renderIcons() {
        createIcons({
            icons,
            attrs: {
                'stroke-width': 2
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderIcons);
    } else {
        renderIcons();
    }

    document.querySelectorAll('canvas[data-config]').forEach((canvas) => {
        const config = JSON.parse(canvas.dataset.config);

        config.options = {
            ...config.options,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                ...config.options?.plugins,
                legend: {
                    ...config.options?.plugins?.legend,
                    display: config.type !== 'doughnut',
                    position: 'bottom',
                },
                tooltip: {
                    ...config.options?.plugins?.tooltip,
                    callbacks: {
                        label: (ctx) => {
                            const value = ctx.chart.config.type === 'doughnut'
                                ? ctx.parsed
                                : ctx.parsed.y;
                            const name = ctx.dataset.label || ctx.label || '';

                            return ` ${name}: ${rupiah(value)}`;
                        },
                    },
                },
            },
        };

        if (config.type === 'bar') {
            config.options.scales ??= {};
            config.options.scales.y ??= {};
            config.options.scales.y.beginAtZero = true;
        }

        const compactMoney = (value) => {
            const n = Number(value);

            if (Math.abs(n) >= 1000000) {
                return (n / 1000000).toLocaleString('en-US', {
                    maximumFractionDigits: 1
                }) + 'M';
            }

            if (Math.abs(n) >= 1000) {
                return (n / 1000).toLocaleString('en-US', {
                    maximumFractionDigits: 0
                }) + 'K';
            }

            return String(n);
        };

        if (config.type === 'bar' || config.type === 'line') {
            config.options ??= {};
            config.options.scales ??= {};
            config.options.scales.y ??= {};
            config.options.scales.y.ticks ??= {};

            config.options.scales.y.ticks.callback = compactMoney;
        }

        new Chart(canvas, config);

        document.addEventListener('DOMContentLoaded', () => {
            const modal = id => document.getElementById(id);
            let previousFocus = null;

            const open = id => {
                const el = modal(id);
                if (!el) return;

                previousFocus = document.activeElement;
                el.hidden = false;
                document.body.style.overflow = 'hidden';
                el.querySelector('button, input, select')?.focus();
            };

            const close = el => {
                if (!el) return;

                el.hidden = true;
                document.body.style.overflow = '';
                previousFocus?.focus();
            };

            document.querySelectorAll('[data-mm-open]').forEach(b => {
                b.addEventListener('click', () => open(b.dataset.mmOpen));
            });

            document.querySelectorAll('[data-mm-close]').forEach(b => {
                b.addEventListener('click', () => close(b.closest('.mm-modal')));
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.mm-modal:not([hidden])').forEach(close);
                }
            });

            const categorySelect = document.getElementById('mm-category');
            const typeButtons = document.querySelectorAll('[data-record-type]');

            const setType = type => {
                typeButtons.forEach(b => b.classList.toggle('selected', b.dataset.recordType === type));
                if (!categorySelect) return;

                [...categorySelect.options].forEach(o => {
                    if (!o.value) return;
                    o.hidden = o.dataset.categoryType !== type;
                    o.disabled = o.dataset.categoryType !== type;
                });

                if (categorySelect.selectedOptions[0]?.disabled) {
                    categorySelect.value = '';
                }
            };

            typeButtons.forEach(b => b.addEventListener('click', () => setType(b.dataset.recordType)));
            setType('expense');

            const tabs = document.querySelectorAll('[data-mm-category-tab]');

            tabs.forEach(b => {
                b.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.toggle('selected', t === b));
                    document.querySelectorAll('[data-mm-category-panel]').forEach(p => {
                        p.hidden = p.dataset.mmCategoryPanel !== b.dataset.mmCategoryTab;
                    });
                });
            });

            document.querySelectorAll('[data-mm-edit-category]').forEach(b => {
                b.addEventListener('click', () => {
                    const f = document.getElementById('mm-category-edit-' + b.dataset.mmEditCategory);
                    if (f) f.hidden = !f.hidden;
                });
            });

            const screen = document.getElementById('mm-calc-screen');
            const amount = document.getElementById('mm-amount');
            let expr = '';

            const show = value => {
                if (screen) screen.textContent = value || '0';
            };

            const evaluate = () => {
                if (!/^[\d+*/.()\s-]+$/.test(expr) || !expr) return null;

                try {
                    const parts = expr.match(/\d+(?:\.\d+)?|[+*/()-]/g);
                    if (!parts || parts.join('') !== expr.replace(/\s/g, '')) return null;

                    const val = Function('"use strict";return (' + expr + ')')();
                    return Number.isFinite(val) ? Math.round(val * 100) / 100 : null;
                } catch {
                    return null;
                }
            };

            document.querySelectorAll('[data-mm-key]').forEach(b => {
                b.addEventListener('click', () => {
                    const k = b.dataset.mmKey;

                    if (k === 'AC') {
                        expr = '';
                    } else if (k === 'DEL') {
                        expr = expr.slice(0, -1);
                    } else if (k === '=') {
                        const n = evaluate();
                        if (n === null) {
                            show('Error');
                            return;
                        }
                        expr = String(n);
                    } else if (k === '%') {
                        const n = evaluate();
                        if (n === null) {
                            show('Error');
                            return;
                        }
                        expr = String(n / 100);
                    } else {
                        expr += k;
                    }

                    show(expr);
                });
            });

            document.getElementById('mm-use-result')?.addEventListener('click', () => {
                const n = evaluate();
                if (n !== null && n > 0 && amount) {
                    amount.value = Math.round(n);
                    amount.focus();
                }
            });

            document.getElementById('mm-add-record')?.addEventListener('submit', e => {
                if (categorySelect && !categorySelect.value) {
                    e.preventDefault();
                    categorySelect.focus();
                    categorySelect.setCustomValidity('Choose a category for the selected type');
                    categorySelect.reportValidity();
                }
            });

            categorySelect?.addEventListener('change', () => categorySelect.setCustomValidity(''));

            if (new URLSearchParams(location.search).has('open')) {
                const target = new URLSearchParams(location.search).get('open');
                if (['record-modal', 'category-modal'].includes(target)) open(target);
            }
        });
    });
});
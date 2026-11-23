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

    document.querySelectorAll('[data-mm-open]').forEach(button => {
        button.addEventListener('click', () => open(button.dataset.mmOpen));
    });

    document.querySelectorAll('[data-mm-close]').forEach(button => {
        button.addEventListener('click', () => close(button.closest('.mm-modal')));
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.mm-modal:not([hidden])').forEach(close);
        }
    });

    const categorySelect = document.getElementById('mm-category');
    const typeButtons = document.querySelectorAll('[data-record-type]');

    const setType = type => {
        typeButtons.forEach(button => {
            button.classList.toggle('selected', button.dataset.recordType === type);
        });

        if (!categorySelect) return;

        [...categorySelect.options].forEach(option => {
            if (!option.value) return;

            option.hidden = option.dataset.categoryType !== type;
            option.disabled = option.dataset.categoryType !== type;
        });

        if (categorySelect.selectedOptions[0]?.disabled) {
            categorySelect.value = '';
        }
    };

    typeButtons.forEach(button => {
        button.addEventListener('click', () => setType(button.dataset.recordType));
    });

    setType('expense');

    const tabs = document.querySelectorAll('[data-mm-category-tab]');

    tabs.forEach(button => {
        button.addEventListener('click', () => {
            tabs.forEach(tab => tab.classList.toggle('selected', tab === button));

            document.querySelectorAll('[data-mm-category-panel]').forEach(panel => {
                panel.hidden = panel.dataset.mmCategoryPanel !== button.dataset.mmCategoryTab;
            });
        });
    });

    document.querySelectorAll('[data-mm-edit-category]').forEach(button => {
        button.addEventListener('click', () => {
            const form = document.getElementById('mm-category-edit-' + button.dataset.mmEditCategory);
            if (form) form.hidden = !form.hidden;
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

            const value = Function('"use strict";return (' + expr + ')')();
            return Number.isFinite(value) ? Math.round(value * 100) / 100 : null;
        } catch {
            return null;
        }
    };

    document.querySelectorAll('[data-mm-key]').forEach(button => {
        button.addEventListener('click', () => {
            const key = button.dataset.mmKey;

            if (key === 'AC') {
                expr = '';
            } else if (key === 'DEL') {
                expr = expr.slice(0, -1);
            } else if (key === '=') {
                const value = evaluate();
                if (value === null) {
                    show('Error');
                    return;
                }
                expr = String(value);
            } else if (key === '%') {
                const value = evaluate();
                if (value === null) {
                    show('Error');
                    return;
                }
                expr = String(value / 100);
            } else {
                expr += key;
            }

            show(expr);
        });
    });

    document.getElementById('mm-use-result')?.addEventListener('click', () => {
        const value = evaluate();

        if (value !== null && value > 0 && amount) {
            amount.value = Math.round(value);
            amount.focus();
        }
    });

    document.getElementById('mm-add-record')?.addEventListener('submit', event => {
        if (categorySelect && !categorySelect.value) {
            event.preventDefault();
            categorySelect.focus();
            categorySelect.setCustomValidity('Choose a category for the selected type');
            categorySelect.reportValidity();
        }
    });

    categorySelect?.addEventListener('change', () => {
        categorySelect.setCustomValidity('');
    });

    if (new URLSearchParams(location.search).has('open')) {
        const target = new URLSearchParams(location.search).get('open');
        if (['record-modal', 'category-modal'].includes(target)) open(target);
    }
});
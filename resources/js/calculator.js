document.addEventListener('DOMContentLoaded', () => {
    const panel = document.getElementById('calc-panel');
    const toggle = document.getElementById('calc-toggle');
    const display = document.getElementById('calc-display');

    if (!panel || !toggle || !display) return;

    let expression = '';

    const render = () => {
        display.textContent = expression === '' ? '0' : expression.replace(/\*/g, '×').replace(/\//g, '÷');
    };

    const evaluate = () => {
        if (expression === '' || !/^[0-9+\-*/.() ]+$/.test(expression)) return;

        try {
            const result = Function('"use strict"; return (' + expression + ')')();

            if (!Number.isFinite(result)) throw new Error('invalid');

            expression = String(Math.round(result * 100) / 100);
        } catch (e) {
            expression = '';
            display.textContent = 'Error';
            return;
        }

        render();
    };

    toggle.addEventListener('click', () => panel.classList.toggle('hidden'));

    panel.addEventListener('click', (event) => {
        const button = event.target.closest('[data-calc]');
        if (!button) return;

        const key = button.dataset.calc;

        if (key === 'C') {
            expression = '';
        } else if (key === 'DEL') {
            expression = expression.slice(0, -1);
        } else if (key === '=') {
            evaluate();
            return;
        } else if (key === 'USE') {
            evaluate();

            const input = document.querySelector('[data-amount-input]');
            const value = Math.round(Number(expression));

            if (input && value > 0) {
                input.value = value;
                input.focus();
                panel.classList.add('hidden');
            }

            return;
        } else {
            expression += key;
        }

        render();
    });

    render();
});

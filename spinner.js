function initSpinners() {
  document.querySelectorAll('input.spinner-only').forEach(input => {
  const min = parseFloat(input.min) || 0;
  const max = parseFloat(input.max) || 100;
  const step = parseFloat(input.step) || 1;

  input.addEventListener('keydown', e => {
    const allowedKeys = ['ArrowUp', 'ArrowDown', 'Tab', 'Shift', 'Backspace', 'Delete', '.', 'Home', 'End'];
    if (!allowedKeys.includes(e.key) && !/^\d$/.test(e.key)) {
      e.preventDefault();
    }
  });

  input.addEventListener('keydown', e => {
    let val = parseFloat(input.value) || min;

    if (e.key === 'ArrowUp') {
      val = Math.min(val + step, max);
      input.value = val;
      e.preventDefault();
    } else if (e.key === 'ArrowDown') {
      val = Math.max(val - step, min);
      input.value = val;
      e.preventDefault();
    }
  });

  input.addEventListener('input', () => {
    if (input.value === '') return;
    let val = parseFloat(input.value);
    if (isNaN(val)) val = '';
    else if (val < min) val = min;
    else if (val > max) val = max;
    input.value = val;
  });

  input.addEventListener('paste', e => {
    const paste = (e.clipboardData || window.clipboardData).getData('text');
    if (!/^\d*\.?\d*$/.test(paste)) e.preventDefault();
  });

  input.addEventListener('wheel', e => e.preventDefault());
});

}

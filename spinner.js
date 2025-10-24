function initSpinners() {
  document.querySelectorAll('input.spinner-only').forEach(input => {
    const min = parseInt(input.min, 10) || 0;
    const max = parseInt(input.max, 10) || 100;

    // Do NOT set default value here
    // if (!input.value || isNaN(parseInt(input.value, 10))) input.value = min;

    // Allow only digits and control keys
    input.addEventListener('keydown', e => {
      const allowedKeys = ['ArrowUp', 'ArrowDown', 'Tab', 'Shift', 'Backspace', 'Delete'];
      if (!allowedKeys.includes(e.key) && !/^\d$/.test(e.key)) {
        e.preventDefault();
      }
    });

    // Handle arrow keys
    input.addEventListener('keydown', e => {
      let val = parseInt(input.value, 10);
      if (isNaN(val)) val = min;

      if (e.key === 'ArrowUp') {
        val = Math.min(val + 1, max);
        input.value = val;
        e.preventDefault();
      } else if (e.key === 'ArrowDown') {
        val = Math.max(val - 1, min);
        input.value = val;
        e.preventDefault();
      }
    });

    // Enforce min/max on input
    input.addEventListener('input', () => {
      if (input.value === '') return; // allow empty
      let val = parseInt(input.value, 10);
      if (isNaN(val)) val = '';
      else if (val < min) val = min;
      else if (val > max) val = max;
      input.value = val;
    });

    // Prevent paste of non-digits
    input.addEventListener('paste', e => {
      const paste = (e.clipboardData || window.clipboardData).getData('text');
      if (!/^\d+$/.test(paste)) e.preventDefault();
    });

    // Disable mouse wheel
    input.addEventListener('wheel', e => e.preventDefault());
  });
}

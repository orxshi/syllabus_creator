function initSpinners() {
  document.querySelectorAll('input.spinner-only').forEach(input => {
    const min = parseInt(input.min, 10) || 0;
    const max = parseInt(input.max, 10) || 100;

    // Set default to min only if empty
    if (!input.value || isNaN(parseInt(input.value, 10))) input.value = min;

    // Prevent manual typing except arrow keys
    input.addEventListener('keydown', e => {
      const allowed = ['ArrowUp', 'ArrowDown', 'Tab', 'Shift'];
      if (!allowed.includes(e.key)) e.preventDefault();
    });

    input.addEventListener('paste', e => e.preventDefault());
    input.addEventListener('wheel', e => e.preventDefault());

    // Enforce min/max if input changes programmatically
    input.addEventListener('input', () => {
      let val = parseInt(input.value, 10);
      if (isNaN(val) || val < min) val = min;
      if (val > max) val = max;
      input.value = val;
    });
  });
}

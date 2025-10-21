function initSpinners() {
  document.querySelectorAll('input.spinner-only').forEach(input => {
    // Make input effectively uneditable but still incrementable
    input.addEventListener('keydown', e => {
      e.preventDefault(); // block all typing
    });

    // Enforce min value (in case of programmatic changes)
    input.addEventListener('input', () => {
      const min = parseInt(input.min || 0, 10);
      if (parseInt(input.value, 10) < min || input.value === '') {
        input.value = min;
      }
    });

    // Optional: prevent mouse wheel changing negative values
    input.addEventListener('wheel', e => e.preventDefault());
  });
}

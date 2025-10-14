function initContribTab() {
  const tableBody = document.querySelector('#tabcontrib tbody');

  // --- Auto-resize function ---
  function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
  }

  function enableAutoResize() {
    document.querySelectorAll('.auto-height').forEach(textarea => {
      autoResizeTextarea(textarea);
      textarea.addEventListener('input', () => autoResizeTextarea(textarea));
    });
  }

  enableAutoResize();

  // --- Load PLOs from .txt file ---
  const fileInput = document.getElementById('ploFile');
  const loadBtn = document.getElementById('loadPLO');

  if (loadBtn && fileInput) {
    loadBtn.addEventListener('click', () => {
      const file = fileInput.files[0];
      if (!file) {
        alert('Please choose a .txt file first.');
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        const lines = e.target.result
          .split(/\r?\n/)
          .map(l => l.trim())
          .filter(l => l !== '');

        const max = Math.min(lines.length, 9); // only fill up to 9

        for (let i = 0; i < max; i++) {
          const textarea = document.getElementById(`contrib${i}`);
          if (textarea) {
            textarea.value = lines[i];
            autoResizeTextarea(textarea);
          }
        }

        if (lines.length > 9) {
          alert('Only the first 9 PLOs were loaded (extra lines ignored).');
        }
      };
      reader.readAsText(file);
    });
  }

  // --- Add new row for department-specific contributions ---
  const addBtn = document.getElementById('addContribRow');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      const rowCount = tableBody.querySelectorAll('tr').length;
      const newRow = document.createElement('tr');
      newRow.innerHTML = `
        <td>
          <textarea id="contrib${rowCount}" name="contrib${rowCount}"
            placeholder="Contribution ${rowCount + 1}"
            class="form-control form-control-sm border-0 p-1 auto-height" rows="1"></textarea>
        </td>
        <td>
          <input class="form-control form-control-sm border-0 p-1"
            type="text" id="contribval${rowCount}" name="contribval${rowCount}" placeholder="">
        </td>
      `;
      tableBody.appendChild(newRow);
      const newTextarea = newRow.querySelector('textarea');
      autoResizeTextarea(newTextarea);
      newTextarea.addEventListener('input', () => autoResizeTextarea(newTextarea));
    });
  }
}

// Wait until DOM is loaded
document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('myform');
    const saveBtn = document.getElementById('save');
    const loadBtn = document.getElementById('load');

    // ===== SAVE FUNCTION =====
    saveBtn.addEventListener('click', (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const jsonData = {};

        // Convert form data to JSON
        for (const [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                const name = key.slice(0, -2);
                if (!jsonData[name]) jsonData[name] = [];
                jsonData[name].push(value);
            } else {
                jsonData[key] = value;
            }
        }

        // Send via AJAX
        fetch('encode.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.text())
        .then(data => {
            window.location.href = 'save.php';
        })
        .catch(err => console.error(err));
    });

    // ===== LOAD FUNCTION =====
    loadBtn.addEventListener('click', (e) => {
        e.preventDefault();

        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.json';
        fileInput.click();

        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function() {
                try {
                    const data = JSON.parse(reader.result);
                    populateForm(data);
                } catch (err) {
                    alert('Invalid JSON file!');
                    console.error(err);
                }
            };
            reader.readAsText(file);
        });
    });

    // ===== POPULATE FORM FUNCTION =====
    function populateForm(data) {

        for (const key in data) {
            const value = data[key];

            // 1️⃣ Handle checkbox groups (name="mode[]")
            const checkboxes = document.querySelectorAll(`input[name="${key}[]"]`);
            if (checkboxes.length) {
                checkboxes.forEach(cb => cb.checked = false);
                if (Array.isArray(value)) {
                    value.forEach(val => {
                        const cb = document.querySelector(`input[name="${key}[]"][value="${val}"]`);
                        if (cb) cb.checked = true;
                    });
                } else {
                    const cb = document.querySelector(`input[name="${key}[]"][value="${value}"]`);
                    if (cb) cb.checked = true;
                }
                continue;
            }

            // 2️⃣ Handle single checkboxes
            const checkbox = document.querySelector(`input[name="${key}"][type="checkbox"]`);
            if (checkbox) {
                checkbox.checked = (value == checkbox.value || value === true);
                continue;
            }

            // 3️⃣ Handle selects
            const select = document.querySelector(`select[name="${key}"]`);
            if (select) {
                select.value = value;
                continue;
            }

            // 4️⃣ Handle assessments table (act0, actper0, act1, etc.)
            if (key.startsWith("act") || key.startsWith("actper")) {
                const input = document.querySelector(`input[name="${key}"]`);
                if (input) input.value = value;
                continue;
            }

            // 5️⃣ Handle ECTS table (ectsact1..ectsactN, ectsnm1.., ectsdur1..)
            if (key.startsWith("ectsact") || key.startsWith("ectsnm") || key.startsWith("ectsdur")) {
                const input = document.querySelector(`input[name="${key}"]`);
                if (input) input.value = value;
                continue;
            }

            // 6️⃣ Handle contributions, outcomes, objectives, sources, content, etc.
            const input = document.querySelector(`input[name="${key}"], textarea[name="${key}"]`);
            if (input) input.value = value;
        }
    }

    // ===== RESET FUNCTION =====
    window.resetAll = function() {
        form.reset();
    };

});

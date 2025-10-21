document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('myform');
    const saveBtn = document.getElementById('save');
    const loadBtn = document.getElementById('load');

    // Disable browser autofill
    form.setAttribute('autocomplete', 'off');
    form.reset();
    form.querySelectorAll('input, textarea, select').forEach(el => el.autocomplete = 'off');

    // ===== SAVE FUNCTION =====
    saveBtn.addEventListener('click', (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const jsonData = {};
        for (const [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                const name = key.slice(0, -2);
                if (!jsonData[name]) jsonData[name] = [];
                jsonData[name].push(value);
            } else jsonData[key] = value;
        }

        // Save JSON
        fetch('encode.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(jsonData)
        })
        .then(() => {
            console.log('JSON saved');

            // Submit for DOCX
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = 'post.php';
            new FormData(form).forEach((val, key) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = val;
                tempForm.appendChild(input);
            });
            const submitWord = document.createElement('input');
            submitWord.type = 'hidden';
            submitWord.name = 'submit_word';
            submitWord.value = '1';
            tempForm.appendChild(submitWord);
            document.body.appendChild(tempForm);
            tempForm.submit();
            tempForm.remove();
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
                try { populateForm(JSON.parse(reader.result)); }
                catch(err) { alert('Invalid JSON file!'); console.error(err); }
            };
            reader.readAsText(file);
        });
    });

    // ===== POPULATE FORM =====
    function populateForm(data) {
        for (const key in data) {
            const value = data[key];

            // Checkbox groups
            const checkboxes = document.querySelectorAll(`input[name="${key}[]"]`);
            if (checkboxes.length) {
                checkboxes.forEach(cb => cb.checked = false);
                if (Array.isArray(value)) value.forEach(val => {
                    const cb = document.querySelector(`input[name="${key}[]"][value="${val}"]`);
                    if (cb) cb.checked = true;
                });
                continue;
            }

            // Single checkbox
            const checkbox = document.querySelector(`input[name="${key}"][type="checkbox"]`);
            if (checkbox) {
                checkbox.checked = (value == checkbox.value || value === true);
                continue;
            }

            // Select
            const select = document.querySelector(`select[name="${key}"]`);
            if (select) { select.value = value; continue; }

            // Input / textarea
            const input = document.querySelector(`input[name="${key}"], textarea[name="${key}"]`);
            if (input) input.value = value;
        }
    }

    // ===== RESET FUNCTION =====
    window.resetAll = function() { form.reset(); };

    // ===== AUTO LOAD JSON IF EXISTS =====
const urlParams = new URLSearchParams(window.location.search);
const courseCode = urlParams.get('course'); // must match filename

if (courseCode) {
    const courseInput = document.getElementById('coursecode');
    if (courseInput) courseInput.value = courseCode;
    // if (courseInput) {
        // courseInput.value = courseCode;   // show the course code
        // courseInput.disabled = true;       // make it read-only
    // }
    

    // Add cache-buster
    const jsonPath = `json/${courseCode}.json?cb=${Date.now()}`;

    fetch(jsonPath, { method: 'HEAD' })
        .then(res => {
            if (res.ok) return fetch(jsonPath).then(r => r.json());
            throw new Error('JSON not found');
        })
        .then(data => populateForm(data))
        .catch(() => {
            console.log(`No JSON found for ${courseCode}, form left empty.`);
            form.reset();
        });
}
});

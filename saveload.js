document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('myform');
    const saveBtn = document.getElementById('save');

    // Disable browser autofill
    form.setAttribute('autocomplete', 'off');
    form.reset();
    form.querySelectorAll('input, textarea, select').forEach(el => el.autocomplete = 'off');

    // ===== SAVE FUNCTION =====
    saveBtn.addEventListener('click', (e) => {
        e.preventDefault(); // prevent form submission

        if (!validateForm()) return; // stop if validation fails

        // Collect form data
        const formData = new FormData(form);
        const jsonData = {};
        for (const [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                const name = key.slice(0, -2);
                if (!jsonData[name]) jsonData[name] = [];
                jsonData[name].push(value);
            } else {
                jsonData[key] = value;
            }
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

    // ===== VALIDATION =====
    function validateForm() {
        let valid = true;
        const invalidFields = [];

        // Example: required fields
        const requiredFields = [
            { id: 'coursename', label: 'Course name' },
            { id: 'coursecode', label: 'Course code' },
            { id: 'yearofstudy', label: 'Year of study' },
            { id: 'semdel', label: 'Semester of delivery' }
        ];

        requiredFields.forEach(field => {
            const el = document.getElementById(field.id);
            if (!el || !el.value.trim()) {
                if (el) el.classList.add('is-invalid');
                invalidFields.push(field.label);
                valid = false;
            } else if (el) el.classList.remove('is-invalid');
        });

        if (!valid && invalidFields.length) {
            alert(`Please check the following fields:\n- ${invalidFields.join('\n- ')}`);
        }

        return valid;
    }

    // ===== POPULATE FORM =====
    window.populateForm = function(data) {
        for (const key in data) {
            const value = data[key];

            // Checkboxes
            const checkboxes = document.querySelectorAll(`input[name="${key}[]"]`);
            if (checkboxes.length) {
                checkboxes.forEach(cb => cb.checked = false);
                if (Array.isArray(value)) value.forEach(val => {
                    const cb = document.querySelector(`input[name="${key}[]"][value="${val}"]`);
                    if (cb) cb.checked = true;
                });
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

    // ===== FETCH JSON =====
    window.fetchAndPopulateJSON = function(courseCode) {
        const jsonPath = `json/${courseCode}.json?cb=${Date.now()}`;
        fetch(jsonPath)
            .then(res => {
                if (!res.ok) throw new Error('JSON not found');
                return res.json();
            })
            .then(data => populateForm(data))
            .catch(() => console.log(`No JSON found for ${courseCode}.`));
    }
});

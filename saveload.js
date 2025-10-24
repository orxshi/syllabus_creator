// =======================
// saveload.js
// =======================

// --- Attach Save Button ---
document.addEventListener("DOMContentLoaded", () => {
    const saveBtn = document.getElementById("save");
    if (saveBtn) saveBtn.addEventListener("click", saveFormData);
});

// --- SAVE FORM DATA TO SERVER AND DOWNLOAD DOCX ---
function saveFormData(e) {
    e?.preventDefault?.();

    const form = document.getElementById("myform");
    if (!form) return console.error("Form not found!");

    const formData = new FormData(form);
    const data = {};

    // --- Basic fields (text, number, select) ---
    for (let [key, value] of formData.entries()) {
        // Skip array fields (checkboxes handled separately)
        if (key.endsWith('[]')) continue;
        data[key] = value;
    }

    // --- Checkbox groups ---
    data["eligdep"] = Array.from(document.querySelectorAll('input[name="eligdep[]"]:checked')).map(el => el.value);
    data["mode"] = Array.from(document.querySelectorAll('input[name="mode[]"]:checked')).map(el => el.value);

    // --- Department select ---
    const deptSelect = document.getElementById("departmentSelect");
    if (deptSelect) data["departmentSelect"] = deptSelect.value;

    document.querySelectorAll('#contribBody tr').forEach((tr, i) => {
    const ploText = tr.children[1].textContent.trim(); // the PLO text
    const checkedRadio = tr.querySelector('input[type="radio"]:checked');
    const value = checkedRadio ? checkedRadio.value : '';

    data[`contrib${i}`] = ploText;      // save text
    data[`contribval${i}`] = value;     // save selected radio
});


    // --- Outcomes ---
    for (let i = 0; i < 8; i++) {
        const outText = document.getElementById(`out${i}`);
        if (outText && outText.value.trim() !== "") data[`out${i}`] = outText.value.trim();

        const outvals = Array.from(document.querySelectorAll(`input[name="outval${i}[]"]:checked`)).map(el => el.value);
        data[`outval${i}`] = outvals;
    }

    // --- Sources (dynamic) ---
    const sourcesContainer = document.getElementById("sourcesContainer");
    if (sourcesContainer) {
        sourcesContainer.querySelectorAll("textarea").forEach((ta, idx) => {
            data[`source${idx}`] = ta.value.trim();
        });
    }

    const courseCode = document.getElementById("coursecode")?.value || "syllabus";

    // --- Save JSON to server (json folder) via encode.php ---
    fetch('encode.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ coursecode: courseCode, ...data }) // <-- flat JSON
    })
    .then(res => res.text())
    .then(() => {
        console.log(`✅ JSON saved as json/${courseCode}.json`);

        // --- Trigger DOCX download via post.php ---
        const tempForm = document.createElement('form');
        tempForm.method = 'POST';
        tempForm.action = 'post.php';

        // Append all fields
        Object.keys(data).forEach(key => {
            if (Array.isArray(data[key])) {
                data[key].forEach(val => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key + '[]';
                    input.value = val;
                    tempForm.appendChild(input);
                });
            } else {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = data[key];
                tempForm.appendChild(input);
            }
        });

        // Signal Word generation
        const submitWord = document.createElement('input');
        submitWord.type = 'hidden';
        submitWord.name = 'submit_word';
        submitWord.value = '1';
        tempForm.appendChild(submitWord);

        document.body.appendChild(tempForm);
        tempForm.submit();
        tempForm.remove();
    })
    .catch(err => {
        console.error("Error saving JSON or generating DOCX:", err);
        alert("⚠️ Could not save form. Check console for details.");
    });
}

// =======================
// LOAD JSON AND POPULATE FORM
// =======================
async function fetchAndPopulateJSON(courseCode) {
    if (!courseCode) return;

    try {
        await new Promise(resolve => setTimeout(resolve, 400));
        const res = await fetch(`json/${courseCode}.json`);
        if (!res.ok) throw new Error("JSON file not found for " + courseCode);

        const data = await res.json(); // flat JSON

        // --- Populate checkboxes ---
        ["eligdep", "mode"].forEach(group => {
            if (data[group]) {
                document.querySelectorAll(`input[name="${group}[]"]`).forEach(cb => {
                    cb.checked = data[group].includes(cb.value);
                });
            }
        });

        // --- Populate basic and select fields ---
        Object.keys(data).forEach(key => {
            if (key.startsWith("source") || key.startsWith("contrib") || key.startsWith("out") || key.startsWith("outval")) return;

            const els = document.querySelectorAll(`[name="${key}"]`);
            els.forEach(el => {
                if (el.type === "checkbox" || el.type === "radio") {
                    el.checked = el.value === data[key];
                } else {
                    el.value = data[key];
                }
            });
        });

        // --- Populate contributions (radios) ---
        initContribTab(data, data.departmentSelect || "fc");

        // --- Populate outcomes ---
        for (let i = 0; i < 8; i++) {
            const outText = document.getElementById(`out${i}`);
            if (outText && data[`out${i}`]) outText.value = data[`out${i}`];

            if (data[`outval${i}`]) {
                document.querySelectorAll(`input[name="outval${i}[]"]`).forEach(cb => {
                    cb.checked = data[`outval${i}`].includes(cb.value);
                });
            }
        }

        // --- Populate sources ---
        const container = document.getElementById("sourcesContainer");
        if (container) {
            Object.keys(data).forEach(key => {
                if (!key.startsWith("source")) return;
                let ta = document.getElementById(key);
                if (!ta) {
                    const div = document.createElement("div");
                    div.classList.add("mb-3");
                    ta = document.createElement("textarea");
                    ta.classList.add("form-control");
                    ta.id = key;
                    ta.name = key;
                    ta.rows = 2;
                    ta.placeholder = `Source ${key.replace("source", "")}`;
                    div.appendChild(ta);
                    container.appendChild(div);
                }
                ta.value = data[key];
            });
        }

        // --- Populate department select ---
        const deptSelect = document.getElementById("departmentSelect");
        if (deptSelect && data["departmentSelect"]) deptSelect.value = data["departmentSelect"];

        console.log("✅ Form populated successfully.");
    } catch (err) {
        console.error("Error loading JSON:", err);
        alert("⚠️ Could not load course data. Check console for details.");
    }
}

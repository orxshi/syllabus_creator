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


    // =======================
    // --- Outcomes (CLOs) ---
    // =======================
    const outcomeRows = document.querySelectorAll("#outcomeContainer tr");
    outcomeRows.forEach((tr, i) => {
        const textArea = tr.querySelector(`textarea[id="out${i}"]`);
        if (textArea && textArea.value.trim() !== "")
            data[`out${i}`] = textArea.value.trim();

        const checkedVals = Array.from(
            tr.querySelectorAll(`input[name="outval${i}[]"]:checked`)
        ).map(cb => cb.value);

        data[`outval${i}`] = checkedVals;
    });

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
async function fetchAndPopulateJSON(data) {
    if (!data) return;

    try {
        // await new Promise(resolve => setTimeout(resolve, 400));
        // const res = await fetch(`json/${courseCode}.json`);
        // if (!res.ok) throw new Error("JSON file not found for " + courseCode);

        // const data = await res.json(); // flat JSON

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

        // --- Populate Sources ---
const srcContainer = document.getElementById("sourcesContainer");
if (!srcContainer) {
    console.warn("Sources container not yet loaded — skipping for now.");
} else {
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
            srcContainer.appendChild(div);
        }
        ta.value = data[key];
    });
}

        // --- Populate contributions (radios) ---
        initContribTab(data, data.departmentSelect || "fc");

        // --- Populate Outcomes ---
const outcomeContainer = document.getElementById("outcomeContainer");
if (!outcomeContainer) {
    console.warn("Outcome container not yet loaded — will populate later.");
    outcomesPending = true;  // <-- Mark that we need to fill this later
} else {
    outcomesPending = false; // container exists now
    let existingRows = outcomeContainer.querySelectorAll("tr").length;
    const outKeys = Object.keys(data).filter(k => /^out\d+$/.test(k));

    // Ensure all needed rows exist
    for (let i = existingRows; i < outKeys.length; i++) {
        document.getElementById("addCLOBtn")?.click();
    }

    // Fill text and checkbox states
    outKeys.forEach(key => {
        const i = parseInt(key.replace("out", ""));
        const ta = document.getElementById(`out${i}`);
        if (ta) ta.value = data[key] || "";

        const outvals = data[`outval${i}`] || [];
        document.querySelectorAll(`input[name="outval${i}[]"]`).forEach(cb => {
            cb.checked = outvals.includes(cb.value);
        });
    });
}

        // --- Populate Assessments (Activities) ---
const assessContainer = document.getElementById("assessContainer");
if (assessContainer) {
    let actCount = assessContainer.querySelectorAll("tr").length;

    Object.keys(data).forEach(key => {
        if (!key.startsWith("act") || key.startsWith("actper")) return;

        const index = parseInt(key.replace("act", ""));
        const activityValue = data[`act${index}`];
        const percentValue = data[`actper${index}`];

        // If we need a new row (beyond existing ones)
        while (assessContainer.querySelectorAll("tr").length <= index) {
            const tr = document.createElement("tr");

            const tdActivity = document.createElement("td");
            const inputActivity = document.createElement("input");
            inputActivity.type = "text";
            inputActivity.name = `act${actCount}`;
            inputActivity.placeholder = `Activity ${actCount + 1}`;
            inputActivity.className = "form-control form-control-sm border-0 p-1";
            tdActivity.appendChild(inputActivity);
            tr.appendChild(tdActivity);

            const tdPercent = document.createElement("td");
            const inputPercent = document.createElement("input");
            inputPercent.type = "number";
            inputPercent.name = `actper${actCount}`;
            inputPercent.step = 1;
            inputPercent.min = 0;
            inputPercent.max = 100;
            inputPercent.className = "form-control form-control-sm border-0 p-1 spinner-only";
            tdPercent.appendChild(inputPercent);
            tr.appendChild(tdPercent);

            assessContainer.appendChild(tr);
            actCount++;
        }

        // Now fill in the values
        const actInput = assessContainer.querySelector(`[name="act${index}"]`);
        const perInput = assessContainer.querySelector(`[name="actper${index}"]`);
        if (actInput) actInput.value = activityValue || "";
        if (perInput) perInput.value = percentValue || "";
    });
}

        // --- Populate department select ---
        const deptSelect = document.getElementById("departmentSelect");
        if (deptSelect && data["departmentSelect"]) deptSelect.value = data["departmentSelect"];
    } catch (err) {
        console.error("Error loading JSON:", err);
        alert("⚠️ Could not load course data. Check console for details.");
    }
}

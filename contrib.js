// contrib.js
async function loadPLOFile(file) {
    const res = await fetch(file);
    if (!res.ok) return [];
    const text = await res.text();
    return text.split(/\r?\n/).filter(line => line.trim() !== "");
}

function createRow(text, index, savedValue = '') {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="text-center">${index + 1}</td>
        <td>${text}</td>
        <td class="text-center"><input type="radio" name="contribval${index}" value="1" ${savedValue === '1' ? 'checked' : ''}></td>
        <td class="text-center"><input type="radio" name="contribval${index}" value="2" ${savedValue === '2' ? 'checked' : ''}></td>
        <td class="text-center"><input type="radio" name="contribval${index}" value="3" ${savedValue === '3' ? 'checked' : ''}></td>
        <td class="text-center"><input type="radio" name="contribval${index}" value="4" ${savedValue === '4' ? 'checked' : ''}></td>
        <td class="text-center"><input type="radio" name="contribval${index}" value="5" ${savedValue === '5' ? 'checked' : ''}></td>
    `;
    return row;
}

async function initContribTab(savedValuesObj = {}, savedDept = "fc") {
    const contribBody = document.getElementById('contribBody');
    const deptSelect = document.getElementById("departmentSelect");
    if (!contribBody || !deptSelect) return;

    deptSelect.value = savedDept;

    const facultyPLOs = await loadPLOFile("plo/plo.txt");
    const deptPLOs = savedDept !== "fc" ? await loadPLOFile(`plo/plo_${savedDept}.txt`) : [];
    let allPLOs = [...facultyPLOs, ...deptPLOs];

    function renderRows(savedValues) {
        contribBody.innerHTML = "";
        allPLOs.forEach((plo, i) => {
            const savedValue = savedValues[`contribval${i}`] || '';
            contribBody.appendChild(createRow(plo, i, savedValue));
        });
    }

    renderRows(savedValuesObj);

    // Avoid multiple listeners
    if (!deptSelect.dataset.listenerAdded) {
        deptSelect.addEventListener("change", async () => {
            const dept = deptSelect.value;
            const savedValuesTemp = {};
            contribBody.querySelectorAll('tr').forEach((tr, i) => {
                const radio = tr.querySelector('input[type="radio"]:checked');
                savedValuesTemp[`contribval${i}`] = radio ? radio.value : '';
            });

            const newDeptPLOs = dept === "fc" ? [] : await loadPLOFile(`plo/plo_${dept}.txt`);
            allPLOs = [...facultyPLOs, ...newDeptPLOs];
            renderRows(savedValuesTemp);
        });
        deptSelect.dataset.listenerAdded = "true";
    }
}

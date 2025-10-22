function initContribTab() {
  const contribBody = document.getElementById("contribBody");
  const deptSelect = document.getElementById("departmentSelect");

  // Create one PLO row
  function createRow(text, index) {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td class="text-center">${index + 1}</td>
      <td>
        <textarea id="contrib${index}" name="contrib${index}"
          class="form-control form-control-sm border-0 p-1 auto-height"
          rows="1">${text}</textarea>
      </td>
      <td class="text-center">
        <input type="number" 
          class="form-control spinner-only form-control-sm text-center border-0"
          id="contribval${index}" name="contribval${index}"
          min="1" max="5" step="1" value="3">
      </td>
    `;
    return row;
  }

  // Load text file and return lines
  async function loadPLOFile(file) {
    const res = await fetch(file);
    if (!res.ok) return []; // return empty array if file not found
    const text = await res.text();
    return text.split(/\r?\n/).filter(line => line.trim() !== "");
  }

  // Load faculty common PLOs initially
  async function loadFacultyPLOs() {
    const facultyPLOs = await loadPLOFile("plo/plo.txt");
    contribBody.innerHTML = "";
    facultyPLOs.forEach((plo, i) => {
      contribBody.appendChild(createRow(plo, i));
    });
    initSpinners();
  }

    // Initial load
  loadFacultyPLOs();

  // When department changes
  deptSelect.addEventListener("change", async () => {
    const dept = deptSelect.value;

    if (dept === "fc") {
      // Faculty common: only load common PLOs
      await loadFacultyPLOs();
      return;
    }

    try {
      const facultyPLOs = await loadPLOFile("plo/plo.txt");
      const deptPLOs = await loadPLOFile(`plo/plo_${dept}.txt`);
      const allPLOs = [...facultyPLOs, ...deptPLOs];

      contribBody.innerHTML = "";
      allPLOs.forEach((plo, i) => {
        contribBody.appendChild(createRow(plo, i));
      });
      initSpinners();
    } catch (err) {
      console.error(err);
      alert("Could not load department-specific PLOs.");
    }
  });
}

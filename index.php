<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Syllabus Creator</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Your consolidated styles -->
  <link href="styles.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <h3 class="mb-4">List of syllabi (docx)</h3>

    <!-- Search field -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search">

    <?php
      $docFolder = 'doc';
      $hasFiles = false;
      $departments = [];

      if (is_dir($docFolder)) {
          $files = array_diff(scandir($docFolder), ['.', '..']);

          foreach ($files as $file) {
              if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'docx') {
                  $hasFiles = true;
                  $displayName = trim(pathinfo($file, PATHINFO_FILENAME));

                  // Determine department by course code prefix
                  if (str_starts_with($displayName, 'CEN')) $dept = 'Computer Engineering';
                  elseif (str_starts_with($displayName, 'EEN')) $dept = 'Electrical & Electronics Engineering';
                  elseif (str_starts_with($displayName, 'ME')) $dept = 'Mechanical Engineering';
                  elseif (str_starts_with($displayName, 'AE')) $dept = 'Automotive Engineering';
                  elseif (str_starts_with($displayName, 'IE')) $dept = 'Industrial Engineering';
                  elseif (str_starts_with($displayName, 'CVEN')) $dept = 'Civil Engineering';
                  elseif (str_starts_with($displayName, 'ESE')) $dept = 'Energy Systems Engineering';
                  elseif (str_starts_with($displayName, 'AIE')) $dept = 'Artificial Intelligence Engineering';
                  elseif (str_starts_with($displayName, 'SE')) $dept = 'Software Engineering';
                  elseif (str_starts_with($displayName, 'QS')) $dept = 'Quantity Surveying';
                  elseif (
                      str_starts_with($displayName, 'MT') ||
                      str_starts_with($displayName, 'PS') ||
                      str_starts_with($displayName, 'CH') ||
                      str_starts_with($displayName, 'ENG')
                  ) $dept = 'Faculty Common';
                  else $dept = 'University Common';

                  $departments[$dept][] = $displayName;
              }
          }
      }

      if ($hasFiles) {
          echo '<div class="accordion" id="courseAccordion">';

          $accordionIndex = 0;
          foreach ($departments as $deptName => $courses) {
              $deptId = 'dept' . $accordionIndex;

              echo '<div class="accordion-item">';
              echo '  <h2 class="accordion-header" id="heading' . $accordionIndex . '">';
              // 🔹 All accordions collapsed by default
              echo '    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $deptId . '" aria-expanded="false" aria-controls="collapse' . $deptId . '">';
              echo        htmlspecialchars($deptName);
              echo '    </button>';
              echo '  </h2>';

              // 🔹 Collapsed section (no "show" class)
              echo '  <div id="collapse' . $deptId . '" class="accordion-collapse collapse" data-bs-parent="#courseAccordion">';
              echo '    <div class="accordion-body p-0">';
              echo '      <div class="list-group list-group-flush">';

              foreach ($courses as $course) {
                  echo '        <div class="list-group-item d-flex justify-content-between align-items-center file-item">';
                  echo '          <span>' . htmlspecialchars($course) . '</span>';
                  echo '          <div>';
                  echo '            <a href="' . $docFolder . '/' . $course . '.docx" class="btn btn-sm btn-outline-success me-2" download>Download</a>';
                  echo '            <a href="form.html?course=' . urlencode($course) . '" class="btn btn-sm btn-outline-primary">Edit</a>';
                  echo '          </div>';
                  echo '        </div>';
              }

              echo '      </div>';
              echo '    </div>';
              echo '  </div>';
              echo '</div>';

              $accordionIndex++;
          }

          echo '</div>';
      } else {
          echo '<p class="text-muted">No DOCX files found in the folder.</p>';
      }
    ?>

    <!-- Button to redirect if file not listed -->
    <a href="form.html" class="btn btn-primary not-found-btn">New syllabus</a>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- JS for live search -->
  <script>
  const searchInput = document.getElementById('searchInput');

  searchInput.addEventListener('input', function() {
    const filter = this.value.trim().toLowerCase();
    const accordions = document.querySelectorAll('.accordion-item');

    let anyMatch = false;

    accordions.forEach(item => {
      const fileItems = item.querySelectorAll('.file-item');
      let deptHasMatch = false;

      fileItems.forEach(course => {
        const code = course.querySelector('span').textContent.trim().toLowerCase();
        const match = code.includes(filter);
        course.classList.toggle('hidden-item', !match);

        if (match) deptHasMatch = true;
      });

      const collapseEl = item.querySelector('.accordion-collapse');
      const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl);

      if (filter === '') {
        // When search is cleared, collapse all
        bsCollapse.hide();
      } else if (deptHasMatch) {
        bsCollapse.show(); // Expand section if it has matches
        anyMatch = true;
      } else {
        bsCollapse.hide(); // Hide sections with no matches
      }
    });

    // Optional: if nothing matches, you could show a small note or toast
    // if (!anyMatch && filter !== '') alert('No matching courses found.');
  });
</script>

</body>
</html>

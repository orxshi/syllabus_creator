<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Syllabus Creator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
  <style>
    body {
      padding: 2rem;
      background-color: #f8f9fa;
    }
    .file-item:hover {
      background-color: #e9ecef;
      cursor: pointer;
    }
    .not-found-btn {
      margin-top: 1.5rem;
    }
    .hidden-item {
      display: none !important;
    }
  </style>
</head>
<body>
  <div class="container">
    <h3 class="mb-4">List of syllabi (docx)</h3>

    <!-- Search field -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search">

    <div class="list-group mb-3" style="max-height: 400px; overflow-y: auto;">
      <?php
        $docFolder = 'doc';
$hasFiles = false;

// Array to store courses by department
$departments = [];

if(is_dir($docFolder)){
    $files = array_diff(scandir($docFolder), ['.', '..']);
    
    foreach($files as $file){
        if(strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'docx'){
            $hasFiles = true;
            $displayName = trim(pathinfo($file, PATHINFO_FILENAME));
            
            // Determine department by course code prefix
            if(str_starts_with($displayName, 'CEN')){
                $dept = 'Computer Engineering';
            } elseif(str_starts_with($displayName, 'EEN')){
                $dept = 'Electrical & Electronics Engineering';
            } elseif(str_starts_with($displayName, 'ME')){
                $dept = 'Mechanical Engineering';
            } elseif(str_starts_with($displayName, 'AE')){
                $dept = 'Automotive Engineering';
            } elseif(str_starts_with($displayName, 'IE')){
                $dept = 'Industrial Engineering';
            } elseif(str_starts_with($displayName, 'CVEN')){
                $dept = 'Civil Engineering';
            } elseif(str_starts_with($displayName, 'ESE')){
                $dept = 'Energy Systems Engineering';
            } elseif(str_starts_with($displayName, 'AIE')){
                $dept = 'Artificial Intelligence Engineering';
            } elseif(str_starts_with($displayName, 'SE')){
                $dept = 'Software Engineering';
            } elseif(str_starts_with($displayName, 'QS')){
                $dept = 'Quantity Surveying';
            } elseif(
                str_starts_with($displayName, 'MT') ||
                str_starts_with($displayName, 'PS') ||
                str_starts_with($displayName, 'CH') ||
                str_starts_with($displayName, 'ENG')
            ){
                $dept = 'Faculty Common';
            } else {
                $dept = 'Universit Common';
            }


            $departments[$dept][] = $displayName;
        }
    }
}

// Display courses categorized by department
if($hasFiles){
    foreach($departments as $deptName => $courses){
        echo '<h5 class="mt-3">' . htmlspecialchars($deptName) . '</h5>';
        foreach($courses as $course){
            echo '<div class="list-group-item d-flex justify-content-between align-items-center file-item">';
            echo '<span>' . htmlspecialchars($course) . '</span>';
            echo '<div>';
            echo '<a href="' . $docFolder . '/' . $course . '.docx" class="btn btn-sm btn-outline-success me-2" download>Download</a>';
            echo '<a href="form.html?course=' . urlencode($course) . '" class="btn btn-sm btn-outline-primary">Edit</a>';
            echo '</div>';
            echo '</div>';
        }
    }
} else {
    echo '<p class="text-muted">No DOCX files found in the folder.</p>';
}
      ?>
    </div>

    <!-- Button to redirect if file not listed -->
    <a href="form.html" class="btn btn-primary not-found-btn">New syllabus</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- JS for live search -->
  <script>
    const searchInput = document.getElementById('searchInput');
    const listItems = document.querySelectorAll('.file-item');

    searchInput.addEventListener('input', function() {
      const filter = this.value.trim().toLowerCase();

      listItems.forEach(item => {
        const code = item.querySelector('span').textContent.trim().toLowerCase();
        // Add/remove hidden class
        if(code.includes(filter)){
          item.classList.remove('hidden-item');
        } else {
          item.classList.add('hidden-item');
        }
      });
    });
  </script>
</body>
</html>

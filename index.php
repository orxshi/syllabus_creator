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
  </style>
</head>
<body>
  <div class="container">
    <h3 class="mb-4">List of syllabi (docx)</h3>

    <div class="list-group mb-3">
      <?php
        $docFolder = 'doc';
        $jsonFolder = 'json';
        $hasFiles = false;

        if(is_dir($docFolder)){
          $files = array_diff(scandir($docFolder), ['.', '..']);
          foreach($files as $file){
    if(pathinfo($file, PATHINFO_EXTENSION) === 'docx'){
        $hasFiles = true;
        $displayName = pathinfo($file, PATHINFO_FILENAME);

        echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
        echo '<span>'.$displayName.'</span>';
        echo '<div>';
        // Download button
        echo '<a href="'.$docFolder.'/'.$file.'" class="btn btn-sm btn-outline-success me-2" download>Download</a>';
        // Always show Edit button
        echo '<a href="form.html?course='.$displayName.'" class="btn btn-sm btn-outline-primary">Edit</a>';
        echo '</div>';
        echo '</div>';
    }
}

        }

        if(!$hasFiles){
          echo '<p class="text-muted">No DOCX files found in the folder.</p>';
        }
      ?>
    </div>

    <!-- Button to redirect if file not listed -->
    <a href="form.html" class="btn btn-primary not-found-btn">Make new syllabus</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

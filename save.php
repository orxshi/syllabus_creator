<?php
$file = '.mycourse.json';

header('Content-Type: application/json');
header("Content-Disposition: attachment; filename=mycourse.json");

ob_clean();
flush();

readfile($file);

unlink($file);
?>

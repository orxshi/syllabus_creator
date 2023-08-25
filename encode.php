<?php
ini_set('display_errors', 1);

$input = $_POST;
$json_str_done = json_encode($input);
$file = '.mycourse.json';
file_put_contents($file, $json_str_done);
?>

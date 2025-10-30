<?php
$jsonFolder = 'json';
if (!is_dir($jsonFolder)) mkdir($jsonFolder, 0777, true);

// Read raw JSON from fetch
$input = json_decode(file_get_contents('php://input'), true);

$filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $input['filename'] ?? 'syllabus');
file_put_contents("json/$filename.json", json_encode($input, JSON_PRETTY_PRINT));


echo "saved";



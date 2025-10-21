<?php
$jsonFolder = 'json';
if (!is_dir($jsonFolder)) mkdir($jsonFolder, 0777, true);

// Read raw JSON from fetch
$input = json_decode(file_get_contents('php://input'), true);

// Use coursecode as filename
$courseCode = preg_replace('/[^a-zA-Z0-9_-]/', '', $input['coursecode'] ?? 'syllabus');

$file = "$jsonFolder/$courseCode.json";
file_put_contents($file, json_encode($input, JSON_PRETTY_PRINT));

echo "saved";

<?php
$lecturerFile = 'lecturer.txt';
$lecturers = [];
if(file_exists($lecturerFile)){
    $lines = file($lecturerFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line){
        $parts = explode(',', $line);
        if(count($parts) === 2){
            $lecturers[] = [
                'initials' => trim($parts[0]),
                'name' => trim($parts[1])
            ];
        }
    }
}
header('Content-Type: application/json');
echo json_encode($lecturers);
<?php
require 'mongo_connection.php';

$data = [];

// Fetch first 100 student grade documents
$grades = iterator_to_array($collection->find([], ['limit' => 100]));

$gpas = [];
foreach ($grades as $doc) {
    $g = (array) $doc['Grades'];
    if (count($g)) {
        $gpas[] = round(array_sum($g) / count($g), 2);
    }
}

// Calculate Mean and Standard Deviation
$mean = array_sum($gpas) / count($gpas);
$std = sqrt(array_sum(array_map(fn($g) => pow($g - $mean, 2), $gpas)) / count($gpas));

// Return student GPA + Z-score
foreach ($grades as $index => $doc) {
    $g = (array) $doc['Grades'];
    if (count($g)) {
        $gpa = round(array_sum($g) / count($g), 2);
        $z = round(($gpa - $mean) / $std, 2);
        $data[] = [
            'student' => 'Student ' . ($doc['StudentID'] ?? $index + 1),
            'gpa' => $gpa,
            'zscore' => $z
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>

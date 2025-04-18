<?php
require 'mongo_connection.php';

$cursor = $collection->find([], ['limit' => 5000]);
$studentGpas = [];

foreach ($cursor as $doc) {
    $studentId = $doc['StudentID'] ?? 'Unknown';
    $grades = (array) $doc['Grades'];
    $weights = isset($doc['Weights']) ? (array) $doc['Weights'] : array_fill(0, count($grades), 1);

    if (count($grades) === 0) continue;

    $weightedSum = 0;
    $weightTotal = 0;

    foreach ($grades as $i => $grade) {
        $w = $weights[$i] ?? 1;
        $weightedSum += $grade * $w;
        $weightTotal += $w;
    }

    $gpa = $weightTotal > 0 ? $weightedSum / $weightTotal : array_sum($grades) / count($grades);
    $studentGpas[$studentId][] = $gpa;
}

// Average GPA per student, filter at-risk
$data = [];
foreach ($studentGpas as $studentId => $gpas) {
    $avg = array_sum($gpas) / count($gpas);
    if ($avg < 75) {
        $data[] = [
            'label' => "Student #$studentId",
            'gpa' => round($avg, 2)
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>

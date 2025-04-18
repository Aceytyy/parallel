<?php
require 'mongo_connection.php';

$type = $_GET['type'] ?? 'gpa';
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : null;
header('Content-Type: application/json');

if ($type === 'performance') {
    $studentId = (int)$_GET['studentId'];
    $gradesColl = $db->grades;
    $subjectsColl = $db->subjects;

    $record = $gradesColl->findOne(['StudentID' => $studentId]);

    if (!$record) {
        echo json_encode(['subjects' => []]);
        exit;
    }

    $grades = $record['Grades'];
    $codes = $record['SubjectCodes'];
    $semId = $record['SemesterID'];

    $cursor = $gradesColl->find(['SemesterID' => $semId], ['limit' => 1000]);
    $subjectStats = [];

    foreach ($cursor as $doc) {
        foreach ($doc['SubjectCodes'] as $i => $code) {
            $grade = $doc['Grades'][$i];
            if (!isset($subjectStats[$code])) $subjectStats[$code] = [];
            $subjectStats[$code][] = $grade;
        }
    }

    $subjectNames = [];
    foreach ($subjectsColl->find() as $subj) {
        $subjectNames[$subj['_id']] = $subj['Description'];
    }

    $result = [];
    foreach ($codes as $i => $code) {
        $studentGrade = $grades[$i];
        $avg = array_sum($subjectStats[$code]) / count($subjectStats[$code]);
        $rate = $studentGrade > $avg ? 'Higher' : ($studentGrade < $avg ? 'Lower' : 'Equal');
        $result[] = [
            'code' => $code,
            'description' => $subjectNames[$code] ?? 'Unknown',
            'grade' => round($studentGrade, 2),
            'classAverage' => round($avg, 2),
            'rate' => $rate
        ];
    }

    echo json_encode(['subjects' => $result]);
}

elseif ($type === 'subject') {
    $collection = $db->grades;
    $subjectCollection = $db->subjects;

    $pipeline = [];
    if ($semester !== null) {
        $pipeline[] = ['$match' => ['SemesterID' => $semester]];
    }

    $pipeline = array_merge($pipeline, [
        ['$unwind' => '$Grades'],
        ['$unwind' => '$SubjectCodes'],
        ['$project' => [
            'grade' => '$Grades',
            'subjectCode' => '$SubjectCodes'
        ]],
        ['$group' => [
            '_id' => '$subjectCode',
            'average' => ['$avg' => '$grade'],
            'passRate' => ['$avg' => ['$cond' => [['$gte' => ['$grade', 75]], 1, 0]]],
            'topGrade' => ['$max' => '$grade'],
            'atRiskCount' => ['$sum' => ['$cond' => [['$lt' => ['$grade', 75]], 1, 0]]],
            'total' => ['$sum' => 1]
        ]],
        ['$sort' => ['_id' => 1]]
    ]);

    $subjects = $subjectCollection->find();
    $subjectDescriptions = [];
    foreach ($subjects as $subj) {
        $subjectDescriptions[$subj['_id']] = $subj['Description'];
    }

    $data = $collection->aggregate($pipeline);
    $result = [];
    foreach ($data as $doc) {
        $desc = $subjectDescriptions[$doc['_id']] ?? 'Unknown';
        $result[] = [
            'label' => $doc['_id'],
            'description' => $desc,
            'average' => round($doc['average'], 2),
            'passRate' => round($doc['passRate'] * 100),
            'topGrade' => $doc['topGrade'],
            'atRisk' => round(($doc['atRiskCount'] / $doc['total']) * 100)
        ];
    }

    echo json_encode($result);
}

else {
    echo json_encode([]);
}
?>

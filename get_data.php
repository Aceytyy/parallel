<?php
require 'mongo_connection.php';

$type = $_GET['type'] ?? 'gpa';
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : null;
header('Content-Type: application/json');

if ($type === 'gpa') {
    $collection = $db->gpa_summary;
    $data = $collection->find([], ['limit' => 200]);
    $result = [];
    foreach ($data as $doc) {
        $label = "Student #{$doc['StudentID']} (Sem {$doc['SemesterID']})";
        $result[] = ['label' => $label, 'gpa' => $doc['GPA']];
    }
    echo json_encode($result);
}

elseif ($type === 'atRisk') {
    $collection = $db->gpa_summary;
    $data = $collection->find(['GPA' => ['$lt' => 80]]);
    $result = [];
    foreach ($data as $doc) {
        $label = "Student #{$doc['StudentID']} (Sem {$doc['SemesterID']})";
        $result[] = ['label' => $label, 'gpa' => $doc['GPA']];
    }
    echo json_encode($result);
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
            'passRate' => ['$avg' => ['$cond' => [['$gte' => ['$grade', 80]], 1, 0]]],
            'topGrade' => ['$max' => '$grade'],
            'atRiskCount' => ['$sum' => ['$cond' => [['$lt' => ['$grade', 80]], 1, 0]]],
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

elseif ($type === 'year') {
    $collection = $db->gpa_summary;
    $pipeline = [
        ['$group' => [
            '_id' => '$SemesterID',
            'avgGPA' => ['$avg' => '$GPA']
        ]],
        ['$sort' => ['_id' => 1]]
    ];
    $data = $collection->aggregate($pipeline);
    $result = [];
    foreach ($data as $doc) {
        $result[] = ['label' => "Semester {$doc['_id']}", 'value' => round($doc['avgGPA'], 2)];
    }
    echo json_encode($result);
}

elseif ($type === 'performance') {
    $studentId = (int)$_GET['studentId'];
    $gradesColl = $db->grades;
    $studentsColl = $db->students;
    $subjectsColl = $db->subjects;

    $record = $gradesColl->findOne(['StudentID' => $studentId]);

    if (!$record) {
        echo json_encode(['subjects' => []]);
        exit;
    }

    $grades = $record['Grades'];
    $codes = $record['SubjectCodes'];
    $semId = $record['SemesterID'];

    $allGrades = iterator_to_array($gradesColl->find(['SemesterID' => $semId]));
    $subjectStats = [];

    foreach ($allGrades as $doc) {
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
            'grade' => $studentGrade,
            'classAverage' => round($avg, 2),
            'rate' => $rate
        ];
    }

    echo json_encode(['subjects' => $result]);
}

elseif ($type === 'summary') {
    $studentsColl = $db->students;
    $gpaColl = $db->gpa_summary;
    $gradesColl = $db->grades;

    $totalStudents = $studentsColl->countDocuments();
    $averageGPA = $gpaColl->aggregate([
        ['$group' => ['_id' => null, 'avg' => ['$avg' => '$GPA']]]
    ])->toArray()[0]['avg'] ?? 0;

    $atRiskCount = $gpaColl->countDocuments(['GPA' => ['$lt' => 75]]);

    $topSubject = $gradesColl->aggregate([
        ['$unwind' => '$Grades'],
        ['$unwind' => '$SubjectCodes'],
        ['$group' => [
            '_id' => '$SubjectCodes',
            'avg' => ['$avg' => '$Grades']
        ]],
        ['$sort' => ['avg' => -1]],
        ['$limit' => 1]
    ])->toArray();

    $subjectId = $topSubject[0]['_id'] ?? 'Unknown';
    $subject = $db->subjects->findOne(['_id' => $subjectId]);
    $subjectName = $subject['Description'] ?? 'Unknown';

    echo json_encode([
        'totalStudents' => $totalStudents,
        'averageGPA' => round($averageGPA, 2),
        'atRiskCount' => $atRiskCount,
        'topSubject' => $subjectName
    ]);
}

else {
    echo json_encode([]);
}
?>

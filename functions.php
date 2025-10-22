<?php

function fixPostAmpersands(array $post): array {
    $cleanPost = [];

    foreach ($post as $key => $value) {
        if (is_string($value)) {
            // Replace only standalone ampersands, leave valid UTF-8 characters intact
            $value = preg_replace('/&(?![a-zA-Z0-9#]{1,8};)/', '&amp;', $value);

            // Replace Microsoft smart quotes with normal quotes
            $value = str_replace(
                ["\xE2\x80\x9C", "\xE2\x80\x9D", "\x93", "\x94"], 
                '"',
                $value
            );

            // Replace smart single quotes with normal single quote
            $value = str_replace(
                ["\xE2\x80\x98", "\xE2\x80\x99", "\x91", "\x92"], 
                "'",
                $value
            );

            $cleanPost[$key] = $value;
        } else {
            $cleanPost[$key] = $value;
        }
    }

    return $cleanPost;
}

function calculateEctsSum(array $postData, int $count = 10): float
{
    $sumEcts = 0.0;

    for ($i = $count - 1; $i >= 0; $i--) {
        $ectsNm  = $postData["ectsnm" . $i] ?? 0;   // numeric multiplier
        $ectsDur = $postData["ectsdur" . $i] ?? 0;  // duration
        
        $sumEcts += floatval($ectsNm) * floatval($ectsDur);
    }

    return $sumEcts;
}

function addCourseRow(
    \PhpOffice\PhpWord\Element\Table $table,
    string $left,
    string $right,
    float $tableWidth,
    array $rowStyle = []    
): void {

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

    $table->addCell(
        $tableWidth * 0.50,
        ['valign' => 'center']
    )->addText($left, ['bold' => true]);

    $table->addCell(
        $tableWidth * 0.50,
        ['valign' => 'center']
    )->addText($right, null);
}

function addObjectivesRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    float $tableWidth,
    array $rowStyle = []    
): void {

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

    $cell = $table->addCell(
        $tableWidth,
        ['valign' => 'center', 'wrapText' => true]
    );

    $cell->addText(
        "Objectives of the Course:",
        ['bold' => true],
        ['spaceBefore' => 100, 'spaceAfter' => 120]
    );

    // Collect objectives from $_POST
    $objectives = [];
    for ($i = 0; $i < 7; $i++) {
        if (!empty($cleanPost["obj" . $i])) {
            $objectives[] = $cleanPost["obj" . $i];
        }
    }

    foreach ($objectives as $objective) {
    $cell->addListItem(
        $objective,
        0, // depth level
        null, // font style
        [
            'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED
        ],
        [
            'spaceBefore' => 100, // twips before paragraph (1/72 inch)
            'spaceAfter'  => 100  // twips after paragraph
        ]
    );
}

}

function addSourcesRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    float $tableWidth,
    array $rowStyle = []    
): void {

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

    $cell = $table->addCell(
        $tableWidth,
        ['valign' => 'center', 'wrapText' => true]
    );

    $cell->addText(
        "Recommended Sources",
        ['bold' => true],
        ['spaceBefore' => 100, 'spaceAfter' => 120]
    );

    $sources = [];
    for ($i = 0; $i < 5; $i++) {
        if (!empty($cleanPost["source" . $i])) {
            $sources[] = $cleanPost["source" . $i];
        }
    }

    foreach ($sources as $source) {
        $cell->addListItem(
            $source,
            0,
            null,
            [
            'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED
        ],
        [
            'spaceBefore' => 100, // twips before paragraph (1/72 inch)
            'spaceAfter'  => 100  // twips after paragraph
        ]
        );
    }
}

function addContentsRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    float $tableWidth,
    array $rowStyle = []    
): void {

    // First row: Course Contents spanning all 4 columns
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $cell = $table->addCell(
        $tableWidth,
        ['valign' => 'center', 'gridSpan' => 4]
    );
    $cell->addText("Course Contents", ['bold' => true]);

    // Second row: Headers
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell($tableWidth * 0.10, ['valign' => 'center'])->addText("Week", []);
    $table->addCell($tableWidth * 0.15); // Chapter
    $table->addCell($tableWidth * 0.65); // Subject
    $table->addCell($tableWidth * 0.10, ['valign' => 'center'])->addText("Exams", []);

    for ($i = 0; $i < 15; $i++) {
        $table->addRow($rowStyle['height'] ?? null, $rowStyle);

        // Week number is generated dynamically
        $week = $i + 1;

        $table->addCell($tableWidth * 0.10, ['valign' => 'center'])->addText($week, []);    
        $table->addCell($tableWidth * 0.15, ['valign' => 'center'])
              ->addText(!empty($cleanPost["conchp" . $i]) ? "Chapter {$cleanPost["conchp" . $i]}" : '', []);
        $table->addCell($tableWidth * 0.65, ['valign' => 'center'])
              ->addText($cleanPost["consub" . $i] ?? '', []);
        $table->addCell($tableWidth * 0.10, ['valign' => 'center'])
              ->addText($cleanPost["conlab" . $i] ?? '', []);
    }
}


function addAssessmentRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    float $tableWidth,
    array $rowStyle = []
): void {

    // Top row: Assessment
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell(
        $tableWidth, // total table width
        ['valign' => 'center', 'gridSpan' => 2]
    )->addText("Assessment", ['bold' => true]);

    // Gather activities and percentages
    $activities = [];
    $activitiesper = [];
    for ($i = 0; $i < 5; $i++) {
        if (!empty($cleanPost["actper" . $i])) {
            $activities[] = $cleanPost["act" . $i];
            $activitiesper[] = $cleanPost["actper" . $i];
        }
    }

    // Determine max width for activity column
    $maxLength = 0;
    foreach ($activities as $activity) {
        $maxLength = max($maxLength, strlen($activity));
    }
    $activityColWidth = $maxLength * 100; // tweak factor if needed    
    $percentColWidth = $tableWidth - $activityColWidth;

    // Add activity rows
    foreach ($activities as $idx => $activity) {
        $table->addRow($rowStyle['height'] ?? null, $rowStyle);
        $table->addCell($activityColWidth)->addText($activity, []);
        $table->addCell($percentColWidth)->addText($activitiesper[$idx] . " %", []);
    }
}





function addECTSRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    float $tableWidth,
    array $rowStyle = []    
): void {

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        $tableWidth,
        ['valign' => 'center', 'gridSpan' => 4]
    );

	$cell->addText(
        "ECTS Allocated Based on the Student Workload",
        ['bold' => true]        
    );

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$ps = [
    'indentation' => [
        'left' => 0
	],
    'align' => 'center'
	];

	$table->addCell($tableWidth * 0.68, ['valign' => 'center'])->addText("Activities", []);
    $table->addCell($tableWidth * 0.10, ['valign' => 'center'])->addText("Number", [], $ps);
	$table->addCell($tableWidth * 0.10, ['valign' => 'center'])->addText("Duration (hour)", [], $ps);
    $table->addCell($tableWidth * 0.12, ['valign' => 'center'])->addText("Total Workload (hour)", [], $ps);

	$ectsacts = [];
$ectsnms = [];
$ectsdurs = [];
$sumects = 0;

for ($i = 0; $i < 10; $i++) {
    if (!empty($cleanPost["ectsact" . $i])) {
        $ectsacts[] = $cleanPost["ectsact" . $i];
        $ectsnms[] = $cleanPost["ectsnm" . $i];
        $ectsdurs[] = $cleanPost["ectsdur" . $i];

        // Use the **last pushed element**, not $i
        $sumects += floatval(end($ectsnms)) * floatval(end($ectsdurs));
    }
}

	foreach ($ectsacts as $idx => $ectsact)
	{
		$table->addRow($rowStyle['height'] ?? null, $rowStyle);

		$table->addCell($tableWidth * 0.68, ['valign' => 'center'])->addText($ectsact, []);
    	$table->addCell($tableWidth * 0.10, ['valign' => 'center'])->addText($ectsnms[$idx], [], $ps);
		$table->addCell($tableWidth * 0.10, ['valign' => 'center'])->addText($ectsdurs[$idx], [], $ps);
    	$table->addCell($tableWidth * 0.12, ['valign' => 'center'])->addText(floatval($ectsnms[$idx]) * floatval($ectsdurs[$idx]), [], $ps);
	}

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        $tableWidth * 0.88,
        ['valign' => 'center', 'gridSpan' => 3]
    );

	$cell->addText("Total Workload", []);

	$cell = $table->addCell($tableWidth * 0.12, ['valign' => 'center'])->addText($sumects, [], $ps);

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        $tableWidth * 0.88,
        ['valign' => 'center', 'gridSpan' => 3]
    );

	$cell->addText("Total Workload/30 (h)", []);

	$cell = $table->addCell($tableWidth * 0.12, ['valign' => 'center'])->addText(number_format($sumects/30,2), [], $ps);

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        $tableWidth * 0.88,
        ['valign' => 'center', 'gridSpan' => 3]
    );

	$cell->addText("ECTS Credit of the Course", []);

	$cell = $table->addCell($tableWidth * 0.12, ['valign' => 'center'])->addText(round($sumects/30), [], $ps);
	

	
// round(calculateEctsSum($_POST)/30)



	// $sumects = 0;

	// for ($i = 9; $i >= 0; $i--)
	// {
	// 	$insertion0 = $_POST["ectsact" . $i];
	// 	$insertion1 = $_POST["ectsnm" . $i];
	// 	$insertion2 = $_POST["ectsdur" . $i];

	// 	$sumects = $sumects + floatval($insertion1) * floatval($insertion2);

	// 	$env = "myects";
	// 	$off = strlen($env) + 2;

	// 	if (!empty($insertion0))
	// 	{
	// 		$pos = strpos($content, $env);
	// 		$content = substr_replace($content, "\makeectsrow{" . $insertion0 . "}{" . $insertion1 . "}{" . $insertion2 . "}{" . $insertion1 * $insertion2 . "}" . PHP_EOL , $pos+$off, 0);
	// 	}
	// }











}







function addContribsRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    float $tableWidth,
    array $rowStyle = []    
): void {

    // Header row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $cell = $table->addCell($tableWidth, ['valign' => 'center', 'gridSpan' => 3]);
    $cell->addText("Course’s Contribution to Program", ['bold' => true]);

    // Sub-header row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell($tableWidth * 0.90, ['valign' => 'center', 'gridSpan' => 2])->addText("", []);
    $table->addCell($tableWidth * 0.10, ['valign' => 'center'])
          ->addText("CL", []);

    // Collect all contributions dynamically
    $i = 0;
    $contribs0 = [];
    $contribs1 = [];

    while (isset($cleanPost["contrib$i"]) && trim($cleanPost["contrib$i"]) !== '') {
        $contribs0[] = $cleanPost["contrib$i"];
        $contribs1[] = isset($cleanPost["contribval$i"]) ? $cleanPost["contribval$i"] : '';
        $i++;
    }

    // Add contribution rows
    foreach ($contribs0 as $idx => $contrib) {
        $table->addRow($rowStyle['height'] ?? null, $rowStyle);

        // Number cell (left)
        $table->addCell($tableWidth * 0.04, ['valign' => 'center'])->addText($idx + 1, []);

        // Contribution text cell (middle)
        $table->addCell($tableWidth * 0.86, ['wrapText' => true, 'valign' => 'center'])
              ->addText($contrib, []);

        // CL cell (right)
        $table->addCell($tableWidth * 0.10, ['valign' => 'center'])
              ->addText($contribs1[$idx] ?? '', []);
    }

    // Footer row for Contribution Level explanation
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell(0, ['gridSpan' => 3, 'valign' => 'center'])
          ->addText(
              "CL: Contribution Level (1: Very Low, 2: Low, 3: Moderate, 4: High, 5: Very High)",
              [],
              ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]
          );
}










function addOutcomesRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    float $tableWidth,
    array $rowStyle = []    
): void {

    // Header row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $cell = $table->addCell($tableWidth, ['valign' => 'center', 'gridSpan' => 3]);
    $cell->addText("Learning Outcomes", ['bold' => true]);

    // Sub-header row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell($tableWidth * 0.90, ['valign' => 'center', 'gridSpan' => 2])
          ->addText("When this course has been completed the student should be able to", []);
    $table->addCell($tableWidth * 0.10, ['valign' => 'center'])
          ->addText("Assess.", []);

    // Collect outcomes
    $outcomes0 = [];
    $outcomes1 = [];
    for ($i = 0; $i < 7; $i++) {
        if (!empty($cleanPost["out" . $i])) {
            $outcomes0[] = $cleanPost["out" . $i];
            $outcomes1[] = $cleanPost["outval" . $i];
        }
    }

    // Add outcome rows
    foreach ($outcomes0 as $idx => $outcome) {
        $table->addRow($rowStyle['height'] ?? null, $rowStyle);

        // Number cell (left)
        $table->addCell($tableWidth * 0.03, ['valign' => 'center'])->addText($idx + 1, []);

        // Outcome text cell (middle) with indent
        $table->addCell($tableWidth * 0.87, ['wrapText' => true, 'valign' => 'center'])
              ->addText($outcome, []);

        // Assessment cell (right)
        $table->addCell($tableWidth * 0.10, ['valign' => 'center'])
              ->addText($outcomes1[$idx] ?? '', []);
    }

    

    // Assessment Methods row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell($tableWidth, ['gridSpan' => 3, 'valign' => 'center'])
          ->addText(
              "Assessment Methods: 1. Written Exam, 2. Assignment, 3. Project/Report, 4. Presentation, 5. Lab Work",
              [],
              ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]
          );
}


?>
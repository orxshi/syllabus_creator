<?php

require_once 'vendor/autoload.php';
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
// \PhpOffice\PhpWord\Settings::setPdfRenderer(
//     \PhpOffice\PhpWord\Settings::PDF_RENDERER_TCPDF,
//     __DIR__ . '/vendor/tecnickcom/tcpdf' // path to TCPDF
// );





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









// function fixPostAmpersands(array $post): array {
//     $cleanPost = [];

//     foreach ($post as $key => $value) {
//         if (is_string($value)) {
//             // Replace ampersand with Word-safe entity
//             $value = str_replace('&', '&amp;', $value);

//             // // Replace Microsoft smart quotes with normal quotes
//             // $value = str_replace(
//             //     ["\xE2\x80\x9C", "\xE2\x80\x9D", "\x93", "\x94"], // UTF-8 and Windows encodings
//             //     '"',
//             //     $value
//             // );

//             // // Replace smart single quotes with normal single quote (optional)
//             // $value = str_replace(
//             //     ["\xE2\x80\x98", "\xE2\x80\x99", "\x91", "\x92"], 
//             //     "'",
//             //     $value
//             // );

//             // Trim whitespace
//             $cleanPost[$key] = trim($value);
//         } else {
//             $cleanPost[$key] = $value;
//         }
//     }

//     return $cleanPost;
// }






// function fixPostAmpersands(array $post): array {
//     $cleanPost = [];

//     foreach ($post as $key => $value) {
//         if (is_string($value)) {
//             // Replace ampersand with Word-safe entity
//             $value = str_replace('&', '&amp;', $value);

//             // Replace Microsoft smart quotes with normal quotes
//             $value = str_replace(
//                 ["\xE2\x80\x9C", "\xE2\x80\x9D", "\x93", "\x94"], // UTF-8 and Windows encodings
//                 '"',
//                 $value
//             );

//             // Replace smart single quotes with normal single quote (optional)
//             $value = str_replace(
//                 ["\xE2\x80\x98", "\xE2\x80\x99", "\x91", "\x92"], 
//                 "'",
//                 $value
//             );

//             // Trim whitespace
//             $cleanPost[$key] = trim($value);
//         } else {
//             $cleanPost[$key] = $value;
//         }
//     }

//     return $cleanPost;
// }







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
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

    $table->addCell(
        4500,
        ['valign' => 'center']
    )->addText($left, ['bold' => true], $paragraphStyle);

    $table->addCell(
        5000,
        ['valign' => 'center', 'wrapText' => true]
    )->addText($right, null, $paragraphStyle);
}

function addObjectivesRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

    $cell = $table->addCell(
        0,
        ['valign' => 'center', 'wrapText' => true, 'gridSpan' => 2]
    );

    $cell->addText(
        "Objectives of the Course:",
        ['bold' => true],
        array_merge($paragraphStyle, ['spaceBefore' => 100, 'spaceAfter' => 120])
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
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

    $cell = $table->addCell(
        9500,
        ['valign' => 'center', 'wrapText' => true]
    );

    $cell->addText(
        "Recommended Sources",
        ['bold' => true],
        array_merge($paragraphStyle, ['spaceBefore' => 100, 'spaceAfter' => 120])
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
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

    // First row: Course Contents spanning all 4 columns
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $cell = $table->addCell(
        0,
        ['valign' => 'center', 'gridSpan' => 4]
    );
    $cell->addText(
        "Course Contents",
        ['bold' => true],
        array_merge($paragraphStyle)
    );

    // Second row: Week | Empty | Empty | Exams
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

    $table->addCell(1000, ['valign' => 'center', 'cellMarginTop' => 0, 'cellMarginBottom' => 0, 'cellMarginLeft' => 0, 'cellMarginRight' => 0])->addText("Week", [], $paragraphStyle);
    $table->addCell(1500, ['cellMarginTop' => 0, 'cellMarginBottom' => 0, 'cellMarginLeft' => 0, 'cellMarginRight' => 0], $paragraphStyle);
	$table->addCell(6000, ['valign' => 'center', 'cellMarginTop' => 0, 'cellMarginBottom' => 0, 'cellMarginLeft' => 0, 'cellMarginRight' => 0], $paragraphStyle);
    $table->addCell(1000, ['valign' => 'center', 'cellMarginTop' => 0, 'cellMarginBottom' => 0, 'cellMarginLeft' => 0, 'cellMarginRight' => 0])->addText("Exams", [], $paragraphStyle);

	$conweeks = [];
	$conchapters = [];
	$consubjects = [];
	$conlabs = [];
    
    for ($i = 0; $i < 15; $i++) {
        // if (!empty($cleanPost["consub" . $i])) {
            $conweeks[] = $cleanPost["conweek" . $i];
            $conchapters[] = $cleanPost["conchp" . $i];
            $consubjects[] = $cleanPost["consub" . $i];
            $conlabs[] = $cleanPost["conlab" . $i];
        // }
    }

	foreach ($conweeks as $idx => $conweek) {
		$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	    $table->addCell(1000, ['valign' => 'center'])->addText(($idx + 1), [], $paragraphStyle);    	
    	$table->addCell(1500, ['valign' => 'center'])->addText(!empty($conchapters[$idx]) ? "Chapter {$conchapters[$idx]}" : '', [], $paragraphStyle);
    	$table->addCell(6000, ['valign' => 'center'])->addText($consubjects[$idx] ?? '', [], $paragraphStyle);
    	$table->addCell(1000, ['valign' => 'center'])->addText($conlabs[$idx] ?? '', [], $paragraphStyle);
	}
}

function addAssessmentRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    array $rowStyle = [],
    array $paragraphStyle = [],
): void {

    // Top row: Assessment
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell(
        9500, // total table width
        ['valign' => 'center', 'gridSpan' => 2]
    )->addText("Assessment", ['bold' => true], $paragraphStyle);

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
    $totalWidth = 9500;
    $percentColWidth = $totalWidth - $activityColWidth;

    // Add activity rows
    foreach ($activities as $idx => $activity) {
        $table->addRow($rowStyle['height'] ?? null, $rowStyle);
        $table->addCell($activityColWidth)->addText($activity, [], $paragraphStyle);
        $table->addCell($percentColWidth)->addText($activitiesper[$idx] . " %", [], $paragraphStyle);
    }

    // Handle case with no activities
    if (empty($activities)) {
        $table->addRow($rowStyle['height'] ?? null, $rowStyle);
        $table->addCell($totalWidth * 0.7)->addText("", [], $paragraphStyle);
        $table->addCell($totalWidth * 0.3)->addText("", [], $paragraphStyle);
    }
}





function addECTSRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        0,
        ['valign' => 'center', 'gridSpan' => 4]
    );

	$cell->addText(
        "ECTS Allocated Based on the Student Workload",
        ['bold' => true],
        $paragraphStyle
    );

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$ps = [
    'spaceBefore' => 0,
    'spaceAfter'  => 0,
    'align' => 'center'
	];

	$psleft = [
    'spaceBefore' => 0,
    'spaceAfter'  => 0,
    'align' => 'left'
	];

	$table->addCell(5700, ['valign' => 'center'])->addText("Activities", [], $ps);
    $table->addCell(1000, ['valign' => 'center'])->addText("Number", [], $ps);
	$table->addCell(1000, ['valign' => 'center'])->addText("Duration (hour)", [], $ps);
    $table->addCell(1500, ['valign' => 'center'])->addText("Total Workload (hour)", [], $ps);

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

		$table->addCell(5700, ['valign' => 'center'])->addText($ectsact, [], $paragraphStyle);
    	$table->addCell(1000, ['valign' => 'center'])->addText($ectsnms[$idx], [], $ps);
		$table->addCell(1000, ['valign' => 'center'])->addText($ectsdurs[$idx], [], $ps);
    	$table->addCell(1500, ['valign' => 'center'])->addText(floatval($ectsnms[$idx]) * floatval($ectsdurs[$idx]), [], $ps);
	}

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        9500,
        ['valign' => 'center', 'gridSpan' => 3]
    );

	$cell->addText("Total Workload", [], $paragraphStyle);

	$cell = $table->addCell(2000, ['valign' => 'center'])->addText($sumects, [], $ps);

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        9500,
        ['valign' => 'center', 'gridSpan' => 3]
    );

	$cell->addText("Total Workload/30 (h)", [], $paragraphStyle);

	$cell = $table->addCell(2000, ['valign' => 'center'])->addText(number_format($sumects/30,2), [], $ps);

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        9500,
        ['valign' => 'center', 'gridSpan' => 3]
    );

	$cell->addText("ECTS Credit of the Course", [], $paragraphStyle);

	$cell = $table->addCell(2000, ['valign' => 'center'])->addText(round($sumects/30), [], $ps);
	

	
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
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

    // Header row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $cell = $table->addCell(0, ['valign' => 'center', 'gridSpan' => 2]);
    $cell->addText("Course’s Contribution to Program", ['bold' => true], $paragraphStyle);

    // Sub-header row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell(8200, ['valign' => 'center'])->addText("", [], $paragraphStyle);
    $table->addCell(1300, ['valign' => 'center'])
          ->addText("CL", [], ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]);

    // Collect contributions
    $contribs0 = [];
    $contribs1 = [];
    for ($i = 0; $i < 9; $i++) {
        if (!empty($cleanPost["contrib" . $i])) {
            $contribs0[] = $cleanPost["contrib" . $i];
            $contribs1[] = $cleanPost["contribval" . $i];
        }
    }

    // Add contribution rows
    foreach ($contribs0 as $idx => $contrib) {
        $table->addRow($rowStyle['height'] ?? null, $rowStyle);

        // Number cell (left)
        $table->addCell(300, [
            'valign' => 'center',
            'borderRightSize' => 6,
            'borderRightColor' => '000000'
        ])->addText($idx + 1, [], ['align' => 'left']);

        // Contribution text cell (middle)
        $table->addCell(7900, ['wrapText' => true, 'valign' => 'center'])
              ->addText($contrib, [], array_merge($paragraphStyle, ['indent' => 200]));

        // CL cell (right)
        $table->addCell(1300, ['valign' => 'center'])
              ->addText($contribs1[$idx] ?? '', [], $paragraphStyle);
    }

    // Footer row for Contribution Level explanation
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell(0, ['gridSpan' => 2, 'valign' => 'center'])
          ->addText(
              "CL: Contribution Level (1: Very Low, 2: Low, 3: Moderate, 4: High, 5: Very High)",
              [],
              ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]
          );
}









function addOutcomesRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

    // Header row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $cell = $table->addCell(0, ['valign' => 'center', 'gridSpan' => 2]);
    $cell->addText("Learning Outcomes", ['bold' => true], $paragraphStyle);

    // Sub-header row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell(8200, ['valign' => 'center'])
          ->addText("When this course has been completed the student should be able to", [], $paragraphStyle);
    $table->addCell(1300, ['valign' => 'center'])
          ->addText("Assessment", [], ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]);

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
        $table->addCell(300, [
            'valign' => 'center',
            'borderRightSize' => 6,
            'borderRightColor' => '000000'
        ])->addText($idx + 1, [], ['align' => 'left']);

        // Outcome text cell (middle) with indent
        $table->addCell(7900, ['wrapText' => true, 'valign' => 'center'])
              ->addText($outcome, [], array_merge($paragraphStyle, ['indent' => 200]));

        // Assessment cell (right)
        $table->addCell(1300, ['valign' => 'center'])
              ->addText($outcomes1[$idx] ?? '', [], $paragraphStyle);
    }

    // Assessment Methods row
    $table->addRow($rowStyle['height'] ?? null, $rowStyle);
    $table->addCell(0, ['gridSpan' => 2, 'valign' => 'center'])
          ->addText(
              "Assessment Methods: 1. Written Exam, 2. Assignment, 3. Project/Report, 4. Presentation, 5. Lab Work",
              [],
              ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]
          );
}


if (array_key_exists('submit_pdf', $_POST))
{
	// $cleanPost = $_POST;
	$cleanPost = fixPostAmpersands($_POST);

    $phpWord = new PhpWord();

	$phpWord->setDefaultFontName('Calibri');
	$phpWord->setDefaultFontSize(10);

	$sectionStyle = [
    'orientation' => 'portrait',
    'marginTop' => 1440,    // 1 inch = 1440 twips
    'marginBottom' => 1440,
    'marginLeft' => 1440,
    'marginRight' => 1440
];

	$section = $phpWord->addSection();

	// Header text
	$headerText = "GAU, Faculty of Engineering";
	$section->addText(
		$headerText,
		['bold' => true, 'size' => 14],               // font style: bold, size 16
		['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER] // paragraph style: centered
	);

	// Optional: add a line break after header
	$section->addTextBreak(1.7);

	$rowStyle = [
    'cantSplit' => true,
    'exactHeight' => true,
    'height' => 300  // default row height for all rows
	];

	$rowStyleML = [
    'cantSplit' => true,
    'exactHeight' => false,
    'height' => 300  // default row height for all rows
	];

	$paragraphStyle = [
    'spaceBefore' => 0,
    'spaceAfter'  => 0,
    'indentation' => [
        'left' => 5
	],    
	];

    $table = $section->addTable([
    'borderSize' => 6,
    'borderColor' => '000000',
    'layout' => 'fixed',
    'alignment' => 'center'
]);


	// $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

	if (!empty($cleanPost['eligdep']))
	{
 	   $eligdep = $cleanPost['eligdep'];

    	// If "All departments" is selected, ignore others
    	if (in_array("All departments", $eligdep)) {
	        $selectedDepartments = ["all departments"];
	    } else {
        	$selectedDepartments = $eligdep; // use the ticked ones
    	}

    	// Example: join them into a string for saving/printing
    	$eligdepString = implode(", ", $selectedDepartments);
		$eligdepString = " for " . $eligdepString;
	}
	else
	{
    	$eligdepString = "None selected";
	}

	if (!empty($cleanPost['mode']))
	{
 		$mode = $cleanPost['mode'];
    	$modeString = implode(", ", $mode);
	}
	else
	{
    	$modeString = "None selected";
	}

	addCourseRow($table, "Course Unit Title", $cleanPost['coursename'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Course Unit Code", $cleanPost['coursecode'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Type of Course Unit", $cleanPost['coursetype'] . $eligdepString, $rowStyle, $paragraphStyle);

	addCourseRow($table, "Level of Course Unit", $cleanPost['level'], $rowStyle, $paragraphStyle);

	$natcre = floatval($cleanPost['theoretical']) + floatval($cleanPost['practice']) / 2 + + floatval($cleanPost['labcre']) / 2;

	addCourseRow($table, "National Credits", $natcre, $rowStyle, $paragraphStyle);


	addCourseRow($table, "Number of ECTS Credits Allocated", round(calculateEctsSum($cleanPost)/30), $rowStyle, $paragraphStyle);
	addCourseRow($table, "Theoretical (hour/week)", $cleanPost['theoretical'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Practice (hour/week)", $cleanPost['practice'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Laboratory (hour/week)", $cleanPost['labcre'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Year of Study", $cleanPost['yearofstudy'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Semester when the course unit is delivered", $cleanPost['semdel'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Mode of Delivery", $modeString, $rowStyle, $paragraphStyle);
	addCourseRow($table, "Language of Instruction", $cleanPost['lang'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Prerequisites and co-requisites", $cleanPost['prerequisite'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Recommended Optional Programme Components", $cleanPost['recom'], $rowStyle, $paragraphStyle);

	addObjectivesRow($table, $cleanPost, $rowStyleML, $paragraphStyle, $cleanPost);

	$outcomestable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$contenttable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$sourcetable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$assesstable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$ectstable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$contribstable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

	addOutcomesRow($outcomestable, $cleanPost, $rowStyleML, $paragraphStyle);
	addContribsRow($outcomestable, $cleanPost, $rowStyleML, $paragraphStyle);
	addContentsRow($contenttable, $cleanPost, $rowStyle, $paragraphStyle);
	addSourcesRow($sourcetable, $cleanPost, $rowStyleML, $paragraphStyle);
	addAssessmentRow($assesstable, $cleanPost, $rowStyle, $paragraphStyle);
	addECTSRow($ectstable, $cleanPost, $rowStyleML, $paragraphStyle);



    



    // Step 1: Save DOCX to a temp file
	$tempDocx = tempnam(sys_get_temp_dir(), 'syllabus') . '.docx';
	$writer = IOFactory::createWriter($phpWord, 'Word2007');
	$writer->save($tempDocx);


    $inputDocx = $tempDocx;
    $outputDir = sys_get_temp_dir();


    // Paths
    $libreoffice = __DIR__ . "\\libre\\program\\soffice.exe";
    $inputDocx = $tempDocx;  // your DOCX file path
    $outputPdf = $tempPdf;   // your PDF file path

    // Run LibreOffice
    $cmd = "\"$libreoffice\" --headless --convert-to pdf --outdir \"$outputDir\" \"$inputDocx\"";
    exec($cmd . " 2>&1", $output, $return_var);

    // LibreOffice names the PDF with same basename
    $generatedPdf = $outputDir . DIRECTORY_SEPARATOR . pathinfo($inputDocx, PATHINFO_FILENAME) . ".pdf";



	// // Step 2: Prepare temp PDF path
	// $tempPdf = tempnam(sys_get_temp_dir(), 'syllabus') . '.pdf';

	// // Step 3: Convert DOCX → PDF using Word COM
	// $word = new COM("Word.Application") or die("Unable to instantiate Word");
	// $word->Visible = 0;
	// $doc = $word->Documents->Open($tempDocx);
	// $doc->ExportAsFixedFormat($tempPdf, 17); // 17 = wdExportFormatPDF
	// $doc->Close(false);
	// $word->Quit();

	if (file_exists($generatedPdf)) {
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"syllabus.pdf\"");
    header("Content-Length: " . filesize($generatedPdf));
    flush();
    readfile($generatedPdf);
    unlink($generatedPdf);
} else {
    error_log("PDF not found: " . $generatedPdf);
    echo "PDF conversion failed.";
}
unlink($inputDocx);
}      















if (array_key_exists('submit_word', $_POST)) {
    // $cleanPost = $_POST;
	$cleanPost = fixPostAmpersands($_POST);

    $phpWord = new PhpWord();

	$phpWord->setDefaultFontName('Calibri');
	$phpWord->setDefaultFontSize(10);

	$sectionStyle = [
    'orientation' => 'portrait',
    'marginTop' => 1440,    // 1 inch = 1440 twips
    'marginBottom' => 1440,
    'marginLeft' => 1440,
    'marginRight' => 1440
];

	$section = $phpWord->addSection();

	// Header text
	$headerText = "GAU, Faculty of Engineering";
	$section->addText(
		$headerText,
		['bold' => true, 'size' => 14],               // font style: bold, size 16
		['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER] // paragraph style: centered
	);

	// Optional: add a line break after header
	$section->addTextBreak(1.7);

	$rowStyle = [
    'cantSplit' => true,
    'exactHeight' => true,
    'height' => 300  // default row height for all rows
	];

	$rowStyleML = [
    'cantSplit' => true,
    'exactHeight' => false,
    'height' => 300  // default row height for all rows
	];

	$paragraphStyle = [
    'spaceBefore' => 0,
    'spaceAfter'  => 0,
    'indentation' => [
        'left' => 5
	],    
	];

    $table = $section->addTable([
    'borderSize' => 6,
    'borderColor' => '000000',
    'layout' => 'fixed',
    'alignment' => 'center'
]);


	// $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

	if (!empty($cleanPost['eligdep']))
	{
 	   $eligdep = $cleanPost['eligdep'];

    	// If "All departments" is selected, ignore others
    	if (in_array("All departments", $eligdep)) {
	        $selectedDepartments = ["all departments"];
	    } else {
        	$selectedDepartments = $eligdep; // use the ticked ones
    	}

    	// Example: join them into a string for saving/printing
    	$eligdepString = implode(", ", $selectedDepartments);
		$eligdepString = " for " . $eligdepString;
	}
	else
	{
    	$eligdepString = "None selected";
	}

	if (!empty($cleanPost['mode']))
	{
 		$mode = $cleanPost['mode'];
    	$modeString = implode(", ", $mode);
	}
	else
	{
    	$modeString = "None selected";
	}

	addCourseRow($table, "Course Unit Title", $cleanPost['coursename'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Course Unit Code", $cleanPost['coursecode'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Type of Course Unit", $cleanPost['coursetype'] . $eligdepString, $rowStyle, $paragraphStyle);

	addCourseRow($table, "Level of Course Unit", $cleanPost['level'], $rowStyle, $paragraphStyle);

	$natcre = floatval($cleanPost['theoretical']) + floatval($cleanPost['practice']) / 2 + + floatval($cleanPost['labcre']) / 2;

	addCourseRow($table, "National Credits", $natcre, $rowStyle, $paragraphStyle);


	addCourseRow($table, "Number of ECTS Credits Allocated", round(calculateEctsSum($cleanPost)/30), $rowStyle, $paragraphStyle);
	addCourseRow($table, "Theoretical (hour/week)", $cleanPost['theoretical'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Practice (hour/week)", $cleanPost['practice'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Laboratory (hour/week)", $cleanPost['labcre'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Year of Study", $cleanPost['yearofstudy'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Semester when the course unit is delivered", $cleanPost['semdel'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Mode of Delivery", $modeString, $rowStyle, $paragraphStyle);
	addCourseRow($table, "Language of Instruction", $cleanPost['lang'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Prerequisites and co-requisites", $cleanPost['prerequisite'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Recommended Optional Programme Components", $cleanPost['recom'], $rowStyle, $paragraphStyle);

	addObjectivesRow($table, $cleanPost, $rowStyleML, $paragraphStyle, $cleanPost);

	$outcomestable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$contenttable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$sourcetable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$assesstable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$ectstable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$contribstable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

	addOutcomesRow($outcomestable, $cleanPost, $rowStyleML, $paragraphStyle);
	addContribsRow($outcomestable, $cleanPost, $rowStyleML, $paragraphStyle);
	addContentsRow($contenttable, $cleanPost, $rowStyle, $paragraphStyle);
	addSourcesRow($sourcetable, $cleanPost, $rowStyleML, $paragraphStyle);
	addAssessmentRow($assesstable, $cleanPost, $rowStyle, $paragraphStyle);
	addECTSRow($ectstable, $cleanPost, $rowStyleML, $paragraphStyle);

    // Step 1: Save DOCX to a temp file
    $tempDocx = tempnam(sys_get_temp_dir(), 'syllabus') . '.docx';
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($tempDocx);

    // Step 2: Serve Word file for download
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    header("Content-Disposition: attachment; filename=\"syllabus.docx\"");
    header("Content-Length: " . filesize($tempDocx));
    flush();
    readfile($tempDocx);

    // Step 3: Clean up temp file
    @unlink($tempDocx);
    exit;
}





?>

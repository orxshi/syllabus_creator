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



// if (array_key_exists('submit_pdf_latex', $_POST))
// {
// 	if (PHP_OS === 'Linux')
// 	{
// 		exec('cp documentoriginal.tex syllabus.tex');
// 	}
// 	else
// 	{
// 		exec('copy documentoriginal.tex syllabus.tex');
// 	}

// 	$path_to_file = 'syllabus.tex';
// 	$content = file_get_contents ($path_to_file);

// 	$content = str_replace ("PHCourseName", $_POST['coursename'], $content);
// 	$content = str_replace ("PHCourseCode", $_POST['coursecode'], $content);
// 	$content = str_replace ("PHNationalCredit", $_POST['nationalcredit'], $content);
// 	//$content = str_replace ("PHECTSCredit", $_POST['ectscredit'], $content);
// 	$content = str_replace ("PHTheoretical", $_POST['theoretical'], $content);
// 	$content = str_replace ("PHCourseType", $_POST['coursetype'], $content);
// 	$content = str_replace ("PHCourseLevel", $_POST['courselevel'], $content);
// 	$content = str_replace ("PHPrerequisite", $_POST['prerequisite'], $content);

// 	for ($i = 7; $i >= 0; $i--)
// 	{
// 		$insertion = $_POST["obj" . $i];

// 		$env = "objectives";
// 		$off = strlen($env) + 2;

// 		if (!empty($insertion))
// 		{
// 			$pos = strpos($content, $env);
// 			$content = substr_replace($content, "\item " . $insertion . PHP_EOL, $pos+$off, 0);
// 		}
// 	}

// 	for ($i = 4; $i >= 0; $i--)
// 	{
// 		$insertion = $_POST["source" . $i];

// 		$env = "textbook";
// 		$off = strlen($env) + 2;

// 		if (!empty($insertion))
// 		{
// 			$pos = strpos($content, $env);
// 			$content = substr_replace($content, "\item " . $insertion . PHP_EOL, $pos+$off, 0);
// 		}
// 	}

// 	for ($i = 4; $i >= 0; $i--)
// 	{
// 		$insertion0 = $_POST["act" . $i];
// 		$insertion1 = $_POST["actper" . $i];

// 		$env = "assessment";
// 		$off = strlen($env) + 2;

// 		if (!empty($insertion0))
// 		{
// 			$pos = strpos($content, $env);
// 			$content = substr_replace($content, "\makerow{" . $insertion0 . "}{" . $insertion1 . "}" . PHP_EOL , $pos+$off, 0);
// 		}
// 	}

// 	for ($i = 7; $i >= 0; $i--)
// 	{
// 		$insertion0 = $_POST["out" . $i];
// 		$insertion1 = $_POST["outval" . $i];

// 		$env = "outcomes";
// 		$off = strlen($env) + 2;

// 		if (!empty($insertion0))
// 		{
// 			$pos = strpos($content, $env);
// 			$content = substr_replace($content, "\makerow{" . $insertion0 . "}{" . $insertion1 . "}" . PHP_EOL , $pos+$off, 0);
// 		}
// 	}

// 	$sumects = 0;

// 	for ($i = 9; $i >= 0; $i--)
// 	{
// 		$insertion0 = $_POST["ectsact" . $i];
// 		$insertion1 = $_POST["ectsnm" . $i];
// 		$insertion2 = $_POST["ectsdur" . $i];

// 		$sumects = $sumects + floatval($insertion1) * floatval($insertion2);

// 		$env = "myects";
// 		$off = strlen($env) + 2;

// 		if (!empty($insertion0))
// 		{
// 			$pos = strpos($content, $env);
// 			$content = substr_replace($content, "\makeectsrow{" . $insertion0 . "}{" . $insertion1 . "}{" . $insertion2 . "}{" . $insertion1 * $insertion2 . "}" . PHP_EOL , $pos+$off, 0);
// 		}
// 	}

// 	$env = "end{myects}";
// 	$off = -strlen($env) + 10;

// 	if (!empty($_POST["ectsact1"]))
// 	{
// 		$pos = strpos($content, $env);
// 		$content = substr_replace($content, "\midrule" . PHP_EOL , $pos+$off, 0);
// 		$pos = strpos($content, $env);
// 		$content = substr_replace($content, "\makeectsrow{Total}{}{}{" . $sumects . "}" . PHP_EOL , $pos+$off, 0);
// 		$pos = strpos($content, $env);
// 		$content = substr_replace($content, "\makeectsrow{Total / 30}{}{}{" . number_format($sumects/30,2) . "}" . PHP_EOL , $pos+$off, 0);
// 		$pos = strpos($content, $env);
// 		$content = substr_replace($content, "\makeectsrow{ECTS credits}{}{}{" . round($sumects/30) . "}" . PHP_EOL , $pos+$off, 0);
// 	}

// 	$content = str_replace ("PHECTSCredit", round($sumects/30), $content);

// 	$anylab = false;

// 	for ($i = 14; $i >= 0; $i--)
// 	{
// 		$insertion3 = $_POST["conlab" . $i];

// 		if ($insertion3 != "")
// 		{
// 			$anylab = true;
// 		}
// 	}

// 	$anychp = false;

// 	for ($i = 14; $i >= 0; $i--)
// 	{
// 		$insertion1 = $_POST["conchp" . $i];

// 		if ($insertion1 != "")
// 		{
// 			$anychp = true;
// 		}
// 	}

// 	if ($anylab == true)
// 	{
// 		if ($anychp == true)
// 		{
// 			$content = str_replace("\\begin{contentswolab}", '', $content);
// 			$content = str_replace("\\end{contentswolab}", '', $content);

// 			$content = str_replace("\\begin{contentswolabwochp}", '', $content);
// 			$content = str_replace("\\end{contentswolabwochp}", '', $content);

// 			$content = str_replace("\\begin{contentswochp}", '', $content);
// 			$content = str_replace("\\end{contentswochp}", '', $content);

// 			for ($i = 14; $i >= 0; $i--)
// 			{
// 				$insertion0 = $_POST["conweek" . $i];
// 				$insertion1 = $_POST["conchp" . $i];
// 				$insertion2 = $_POST["consub" . $i];
// 				$insertion3 = $_POST["conlab" . $i];

// 				$env = "contents";
// 				$off = strlen($env) + 2;

// 				if (!empty($insertion2))
// 				{
// 					$pos = strpos($content, $env);
// 					$content = substr_replace($content, "\makeectsrow{" . $insertion0 . "}{" . $insertion1 . "}{" . $insertion2 . "}{" . $insertion3 . "}" . PHP_EOL , $pos+$off, 0);
// 				}
// 			}
// 		}
// 		else
// 		{
// 			$content = str_replace("\\begin{contents}", '', $content);
// 			$content = str_replace("\\end{contents}", '', $content);

// 			$content = str_replace("\\begin{contentswolab}", '', $content);
// 			$content = str_replace("\\end{contentswolab}", '', $content);

// 			$content = str_replace("\\begin{contentswolabwochp}", '', $content);
// 			$content = str_replace("\\end{contentswolabwochp}", '', $content);

// 			for ($i = 14; $i >= 0; $i--)
// 			{
// 				$insertion0 = $_POST["conweek" . $i];
// 				$insertion1 = $_POST["conchp" . $i];
// 				$insertion2 = $_POST["consub" . $i];
// 				$insertion3 = $_POST["conlab" . $i];

// 				$env = "contentswochp";
// 				$off = strlen($env) + 2;

// 				if (!empty($insertion2))
// 				{
// 					$pos = strpos($content, $env);
// 					$content = substr_replace($content, "\makeshortectsrow{" . $insertion0 . "}{" . $insertion2 . "}{" . $insertion3 . "}" . PHP_EOL , $pos+$off, 0);
// 				}
// 			}
// 		}
// 	}
// 	else
// 	{
// 		if ($anychp == true)
// 		{
// 			$content = str_replace("\\begin{contents}", '', $content);
// 			$content = str_replace("\\end{contents}", '', $content);

// 			$content = str_replace("\\begin{contentswolabwochp}", '', $content);
// 			$content = str_replace("\\end{contentswolabwochp}", '', $content);

// 			$content = str_replace("\\begin{contentswochp}", '', $content);
// 			$content = str_replace("\\end{contentswochp}", '', $content);

// 			for ($i = 14; $i >= 0; $i--)
// 			{
// 				$insertion0 = $_POST["conweek" . $i];
// 				$insertion1 = $_POST["conchp" . $i];
// 				$insertion2 = $_POST["consub" . $i];
// 				$insertion3 = $_POST["conlab" . $i];

// 				$env = "contentswolab";
// 				$off = strlen($env) + 2;

// 				if (!empty($insertion2))
// 				{
// 					$pos = strpos($content, $env);
// 					$content = substr_replace($content, "\makeshortectsrow{" . $insertion0 . "}{" . $insertion1 . "}{" . $insertion2 . "}" . PHP_EOL , $pos+$off, 0);
// 				}
// 			}
// 		}
// 		else
// 		{
// 			$content = str_replace("\\begin{contents}", '', $content);
// 			$content = str_replace("\\end{contents}", '', $content);

// 			$content = str_replace("\\begin{contentswolab}", '', $content);
// 			$content = str_replace("\\end{contentswolab}", '', $content);

// 			$content = str_replace("\\begin{contentswochp}", '', $content);
// 			$content = str_replace("\\end{contentswochp}", '', $content);

// 			for ($i = 14; $i >= 0; $i--)
// 			{
// 				$insertion0 = $_POST["conweek" . $i];
// 				$insertion1 = $_POST["conchp" . $i];
// 				$insertion2 = $_POST["consub" . $i];
// 				$insertion3 = $_POST["conlab" . $i];

// 				$env = "contentswolabwochp";
// 				$off = strlen($env) + 2;

// 				if (!empty($insertion2))
// 				{
// 					$pos = strpos($content, $env);
// 					$content = substr_replace($content, "\makeveryshortectsrow{" . $insertion0 . "}{" . $insertion2 . "}" . PHP_EOL , $pos+$off, 0);
// 				}
// 			}
// 		}
// 	}


// 	file_put_contents($path_to_file, $content);

// 	if (PHP_OS === 'Linux')
// 	{
// 		exec('/usr/local/texlive/2021/bin/x86_64-linux/pdflatex syllabus.tex');
// 	}
// 	else
// 	{
// 		exec('C:\Users\orhan\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe syllabus.tex');
// 	}

// 	$file = 'syllabus.pdf';

// 	header('Content-Type: application/pdf');
// 	header("Content-Disposition: inline; filename=\"$file\"");

// 	ob_clean();
// 	flush();

// 	readfile($file);
// }


function addCourseRow(
    \PhpOffice\PhpWord\Element\Table $table,
    string $left,
    string $right,
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

    $table->addCell(
        6500,
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

    // Add bullet points
    foreach ($objectives as $objective) {
        $cell->addListItem(
            $objective,
            0,
            null,
            ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]
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
        11500,
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
            ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]
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
    $table->addCell(2000, ['cellMarginTop' => 0, 'cellMarginBottom' => 0, 'cellMarginLeft' => 0, 'cellMarginRight' => 0], $paragraphStyle);
	$table->addCell(6500, ['valign' => 'center', 'cellMarginTop' => 0, 'cellMarginBottom' => 0, 'cellMarginLeft' => 0, 'cellMarginRight' => 0], $paragraphStyle);
    $table->addCell(2000, ['valign' => 'center', 'cellMarginTop' => 0, 'cellMarginBottom' => 0, 'cellMarginLeft' => 0, 'cellMarginRight' => 0])->addText("Exams", [], $paragraphStyle);

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
    	$table->addCell(2000, ['valign' => 'center'])->addText(!empty($conchapters[$idx]) ? "Chapter {$conchapters[$idx]}" : '', [], $paragraphStyle);
    	$table->addCell(6500, ['valign' => 'center'])->addText($consubjects[$idx] ?? '', [], $paragraphStyle);
    	$table->addCell(2000, ['valign' => 'center'])->addText($conlabs[$idx] ?? '', [], $paragraphStyle);
	}
}

function addAssessmentRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    array $rowStyle = [],
    array $paragraphStyle = [],
): void {

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        11500,
        ['valign' => 'center', 'gridSpan' => 2]
    );

	$cell->addText(
        "Assessment",
        ['bold' => true],
        $paragraphStyle
    );

	$activities = [];
	$activitiesper = [];
    for ($i = 0; $i < 5; $i++) {
        if (!empty($cleanPost["actper" . $i])) {
            $activities[] = $cleanPost["act" . $i];
            $activitiesper[] = $cleanPost["actper" . $i];
        }
    }

	

	foreach ($activities as $idx => $activity) {
		$table->addRow($rowStyle['height'] ?? null, $rowStyle);

		// $leftWidth = max(2000, strlen($activity) * 150);
		$table->addCell(2000)->addText($activity, [], $paragraphStyle);
		$table->addCell(9500)->addText($activitiesper[$idx] . " %", [], $paragraphStyle);
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

	$table->addCell(5500, ['valign' => 'center'])->addText("Activities", [], $ps);
    $table->addCell(2000, ['valign' => 'center'])->addText("Number", [], $ps);
	$table->addCell(2000, ['valign' => 'center'])->addText("Duration (hour)", [], $ps);
    $table->addCell(2000, ['valign' => 'center'])->addText("Total Workload (hour)", [], $ps);

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

		$table->addCell(5500, ['valign' => 'center'])->addText($ectsact, [], $paragraphStyle);
    	$table->addCell(2000, ['valign' => 'center'])->addText($ectsnms[$idx], [], $ps);
		$table->addCell(2000, ['valign' => 'center'])->addText($ectsdurs[$idx], [], $ps);
    	$table->addCell(2000, ['valign' => 'center'])->addText(floatval($ectsnms[$idx]) * floatval($ectsdurs[$idx]), [], $ps);
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

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        0,
        ['valign' => 'center', 'gridSpan' => 2]
    );

	$cell->addText(
        "Course’s Contribution to Program",
        ['bold' => true],
        $paragraphStyle
    );

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$table->addCell(
        9500,
		['valign' => 'center']      
    )->addText("", [], $paragraphStyle);

	$table->addCell(
    2000,
    ['valign' => 'center']
	)->addText(
		"CL",
		[],
		['align' => 'center', 'spaceBefore' => 0, 'spaceAfter'  => 0]
	);



    $contribs0 = [];
    $contribs1 = [];
    for ($i = 0; $i < 9; $i++) {
        if (!empty($cleanPost["contrib" . $i])) {
            $contribs0[] = $cleanPost["contrib" . $i];
            $contribs1[] = $cleanPost["contribval" . $i];
        }
    }

    foreach ($contribs0 as $idx => $contrib) {
    $table->addRow();

    // LEFT cell: nested table for number + text
    $outerCell = $table->addCell(9500);

    $innerTable = $outerCell->addTable(['width' => 9500, 'unit' => 'dxa']);
    $innerTable->addRow();

    // Number cell with RIGHT BORDER
    $innerTable->addCell(800, [
        'borderRightSize'  => 6,
        'borderRightColor' => '000000',
		'valign' => 'center'
    ])->addText(($idx + 1), [], $paragraphStyle);

    // Outcome text cell
    $innerTable->addCell(8700, ['wrapText' => true])
               ->addText($contrib, [], $paragraphStyle);

    // RIGHT cell: assessment
    $table->addCell(2000, ['valign' => 'center'])
          ->addText($contribs1[$idx] ?? '', [], $paragraphStyle);
}

// Add row for Assessment Methods
$table->addRow($rowStyle['height'] ?? null, $rowStyle);



$table->addCell(
    0,
    [
        'gridSpan' => 2,
        'valign'   => 'center'        
    ]
	)->addText(
		"CL: Contribution Level (1: Very Low, 2: Low, 3: Moderate 4: High, 5: Very High)",
		[],
		['align' => 'center', 'spaceBefore' => 0, 'spaceAfter'  => 0]
	);


}








function addOutcomesRow(
    \PhpOffice\PhpWord\Element\Table $table,
    array $cleanPost,
    array $rowStyle = [],
    array $paragraphStyle = []
): void {

    $table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$cell = $table->addCell(
        0,
        ['valign' => 'center', 'gridSpan' => 2]
    );

	$cell->addText(
        "Learning Outcomes",
        ['bold' => true],
        $paragraphStyle
    );

	$table->addRow($rowStyle['height'] ?? null, $rowStyle);

	$table->addCell(
        9500,
		['valign' => 'center']      
    )->addText("When this course has been completed the student should be able to", [], $paragraphStyle);

	$table->addCell(
    2000,
    ['valign' => 'center']
	)->addText(
		"Assessment",
		[],
		['align' => 'center', 'spaceBefore' => 0, 'spaceAfter'  => 0]
	);



    $outcomes0 = [];
    $outcomes1 = [];
    for ($i = 0; $i < 7; $i++) {
        if (!empty($cleanPost["out" . $i])) {
            $outcomes0[] = $cleanPost["out" . $i];
            $outcomes1[] = $cleanPost["outval" . $i];
        }
    }

    foreach ($outcomes0 as $idx => $outcome) {
    $table->addRow();

    // LEFT cell: nested table for number + text
    $outerCell = $table->addCell(9500);

    $innerTable = $outerCell->addTable(['width' => 9500, 'unit' => 'dxa']);
    $innerTable->addRow();

    // Number cell with RIGHT BORDER
    $innerTable->addCell(800, [
        'borderRightSize'  => 6,
        'borderRightColor' => '000000',
		'valign' => 'center'
    ])->addText(($idx + 1), [], $paragraphStyle);

    // Outcome text cell
    $innerTable->addCell(8700, ['wrapText' => true])
               ->addText($outcome, [], $paragraphStyle);

    // RIGHT cell: assessment
    $table->addCell(2000, ['valign' => 'center'])
          ->addText($outcomes1[$idx] ?? '', [], $paragraphStyle);
}

// Add row for Assessment Methods
$table->addRow($rowStyle['height'] ?? null, $rowStyle);



$table->addCell(
    0,
    [
        'gridSpan' => 2,
        'valign'   => 'center'        
    ]
	)->addText(
		"Assessment Methods: 1. Written Exam, 2. Assignment, 3. Project/Report, 4. Presentation, 5. Lab Work",
		[],
		['align' => 'center', 'spaceBefore' => 0, 'spaceAfter'  => 0]
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
		['bold' => true, 'size' => 16],               // font style: bold, size 16
		['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER] // paragraph style: centered
	);

	// Optional: add a line break after header
	$section->addTextBreak(0.7);

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
        'left' => 100
	],    
	];

	$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

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

	$natcre = floatval($cleanPost['theoretical']) + floatval($$cleanPost['practice']) / 2 + + floatval($cleanPost['labcre']) / 2;

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

	addOutcomesRow($outcomestable, $cleanPost, $rowStyle, $paragraphStyle);
	addContribsRow($outcomestable, $cleanPost, $rowStyle, $paragraphStyle);
	addContentsRow($contenttable, $cleanPost, $rowStyle, $paragraphStyle);
	addSourcesRow($sourcetable, $cleanPost, $rowStyleML, $paragraphStyle);
	addAssessmentRow($assesstable, $cleanPost, $rowStyle, $paragraphStyle);
	addECTSRow($ectstable, $cleanPost, $rowStyleML, $paragraphStyle);

    // Step 1: Save DOCX to a temp file
	$tempDocx = tempnam(sys_get_temp_dir(), 'syllabus') . '.docx';
	$writer = IOFactory::createWriter($phpWord, 'Word2007');
	$writer->save($tempDocx);

	// Step 2: Prepare temp PDF path
	$tempPdf = tempnam(sys_get_temp_dir(), 'syllabus') . '.pdf';

	// Step 3: Convert DOCX → PDF using Word COM
	$word = new COM("Word.Application") or die("Unable to instantiate Word");
	$word->Visible = 0;
	$doc = $word->Documents->Open($tempDocx);
	$doc->ExportAsFixedFormat($tempPdf, 17); // 17 = wdExportFormatPDF
	$doc->Close(false);
	$word->Quit();

	// Step 4: Serve PDF for download
	header("Content-Type: application/pdf");
	header("Content-Disposition: attachment; filename=\"syllabus.pdf\"");
	header("Content-Length: " . filesize($tempPdf));
	flush();
	readfile($tempPdf);

	// Step 5: Clean up temp files
	@unlink($tempDocx);
	@unlink($tempPdf);
	exit;
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
		['bold' => true, 'size' => 16],               // font style: bold, size 16
		['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER] // paragraph style: centered
	);

	// Optional: add a line break after header
	$section->addTextBreak(0.7);

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
        'left' => 100
	],    
	];

	$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

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

	$natcre = floatval($cleanPost['theoretical']) + floatval($$cleanPost['practice']) / 2 + + floatval($cleanPost['labcre']) / 2;

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

	addOutcomesRow($outcomestable, $cleanPost, $rowStyle, $paragraphStyle);
	addContribsRow($outcomestable, $cleanPost, $rowStyle, $paragraphStyle);
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

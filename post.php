<?php

require_once 'vendor/autoload.php';
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
\PhpOffice\PhpWord\Settings::setPdfRenderer(
    \PhpOffice\PhpWord\Settings::PDF_RENDERER_TCPDF,
    __DIR__ . '/vendor/tecnickcom/tcpdf' // path to TCPDF
);




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



if (array_key_exists('submit_pdf_latex', $_POST))
{
	if (PHP_OS === 'Linux')
	{
		exec('cp documentoriginal.tex syllabus.tex');
	}
	else
	{
		exec('copy documentoriginal.tex syllabus.tex');
	}

	$path_to_file = 'syllabus.tex';
	$content = file_get_contents ($path_to_file);

	$content = str_replace ("PHCourseName", $_POST['coursename'], $content);
	$content = str_replace ("PHCourseCode", $_POST['coursecode'], $content);
	$content = str_replace ("PHNationalCredit", $_POST['nationalcredit'], $content);
	//$content = str_replace ("PHECTSCredit", $_POST['ectscredit'], $content);
	$content = str_replace ("PHTheoretical", $_POST['theoretical'], $content);
	$content = str_replace ("PHCourseType", $_POST['coursetype'], $content);
	$content = str_replace ("PHCourseLevel", $_POST['courselevel'], $content);
	$content = str_replace ("PHPrerequisite", $_POST['prerequisite'], $content);

	for ($i = 7; $i >= 0; $i--)
	{
		$insertion = $_POST["obj" . $i];

		$env = "objectives";
		$off = strlen($env) + 2;

		if (!empty($insertion))
		{
			$pos = strpos($content, $env);
			$content = substr_replace($content, "\item " . $insertion . PHP_EOL, $pos+$off, 0);
		}
	}

	for ($i = 4; $i >= 0; $i--)
	{
		$insertion = $_POST["source" . $i];

		$env = "textbook";
		$off = strlen($env) + 2;

		if (!empty($insertion))
		{
			$pos = strpos($content, $env);
			$content = substr_replace($content, "\item " . $insertion . PHP_EOL, $pos+$off, 0);
		}
	}

	for ($i = 4; $i >= 0; $i--)
	{
		$insertion0 = $_POST["act" . $i];
		$insertion1 = $_POST["actper" . $i];

		$env = "assessment";
		$off = strlen($env) + 2;

		if (!empty($insertion0))
		{
			$pos = strpos($content, $env);
			$content = substr_replace($content, "\makerow{" . $insertion0 . "}{" . $insertion1 . "}" . PHP_EOL , $pos+$off, 0);
		}
	}

	for ($i = 7; $i >= 0; $i--)
	{
		$insertion0 = $_POST["out" . $i];
		$insertion1 = $_POST["outval" . $i];

		$env = "outcomes";
		$off = strlen($env) + 2;

		if (!empty($insertion0))
		{
			$pos = strpos($content, $env);
			$content = substr_replace($content, "\makerow{" . $insertion0 . "}{" . $insertion1 . "}" . PHP_EOL , $pos+$off, 0);
		}
	}

	$sumects = 0;

	for ($i = 9; $i >= 0; $i--)
	{
		$insertion0 = $_POST["ectsact" . $i];
		$insertion1 = $_POST["ectsnm" . $i];
		$insertion2 = $_POST["ectsdur" . $i];

		$sumects = $sumects + floatval($insertion1) * floatval($insertion2);

		$env = "myects";
		$off = strlen($env) + 2;

		if (!empty($insertion0))
		{
			$pos = strpos($content, $env);
			$content = substr_replace($content, "\makeectsrow{" . $insertion0 . "}{" . $insertion1 . "}{" . $insertion2 . "}{" . $insertion1 * $insertion2 . "}" . PHP_EOL , $pos+$off, 0);
		}
	}

	$env = "end{myects}";
	$off = -strlen($env) + 10;

	if (!empty($_POST["ectsact1"]))
	{
		$pos = strpos($content, $env);
		$content = substr_replace($content, "\midrule" . PHP_EOL , $pos+$off, 0);
		$pos = strpos($content, $env);
		$content = substr_replace($content, "\makeectsrow{Total}{}{}{" . $sumects . "}" . PHP_EOL , $pos+$off, 0);
		$pos = strpos($content, $env);
		$content = substr_replace($content, "\makeectsrow{Total / 30}{}{}{" . number_format($sumects/30,2) . "}" . PHP_EOL , $pos+$off, 0);
		$pos = strpos($content, $env);
		$content = substr_replace($content, "\makeectsrow{ECTS credits}{}{}{" . round($sumects/30) . "}" . PHP_EOL , $pos+$off, 0);
	}

	$content = str_replace ("PHECTSCredit", round($sumects/30), $content);

	$anylab = false;

	for ($i = 14; $i >= 0; $i--)
	{
		$insertion3 = $_POST["conlab" . $i];

		if ($insertion3 != "")
		{
			$anylab = true;
		}
	}

	$anychp = false;

	for ($i = 14; $i >= 0; $i--)
	{
		$insertion1 = $_POST["conchp" . $i];

		if ($insertion1 != "")
		{
			$anychp = true;
		}
	}

	if ($anylab == true)
	{
		if ($anychp == true)
		{
			$content = str_replace("\\begin{contentswolab}", '', $content);
			$content = str_replace("\\end{contentswolab}", '', $content);

			$content = str_replace("\\begin{contentswolabwochp}", '', $content);
			$content = str_replace("\\end{contentswolabwochp}", '', $content);

			$content = str_replace("\\begin{contentswochp}", '', $content);
			$content = str_replace("\\end{contentswochp}", '', $content);

			for ($i = 14; $i >= 0; $i--)
			{
				$insertion0 = $_POST["conweek" . $i];
				$insertion1 = $_POST["conchp" . $i];
				$insertion2 = $_POST["consub" . $i];
				$insertion3 = $_POST["conlab" . $i];

				$env = "contents";
				$off = strlen($env) + 2;

				if (!empty($insertion2))
				{
					$pos = strpos($content, $env);
					$content = substr_replace($content, "\makeectsrow{" . $insertion0 . "}{" . $insertion1 . "}{" . $insertion2 . "}{" . $insertion3 . "}" . PHP_EOL , $pos+$off, 0);
				}
			}
		}
		else
		{
			$content = str_replace("\\begin{contents}", '', $content);
			$content = str_replace("\\end{contents}", '', $content);

			$content = str_replace("\\begin{contentswolab}", '', $content);
			$content = str_replace("\\end{contentswolab}", '', $content);

			$content = str_replace("\\begin{contentswolabwochp}", '', $content);
			$content = str_replace("\\end{contentswolabwochp}", '', $content);

			for ($i = 14; $i >= 0; $i--)
			{
				$insertion0 = $_POST["conweek" . $i];
				$insertion1 = $_POST["conchp" . $i];
				$insertion2 = $_POST["consub" . $i];
				$insertion3 = $_POST["conlab" . $i];

				$env = "contentswochp";
				$off = strlen($env) + 2;

				if (!empty($insertion2))
				{
					$pos = strpos($content, $env);
					$content = substr_replace($content, "\makeshortectsrow{" . $insertion0 . "}{" . $insertion2 . "}{" . $insertion3 . "}" . PHP_EOL , $pos+$off, 0);
				}
			}
		}
	}
	else
	{
		if ($anychp == true)
		{
			$content = str_replace("\\begin{contents}", '', $content);
			$content = str_replace("\\end{contents}", '', $content);

			$content = str_replace("\\begin{contentswolabwochp}", '', $content);
			$content = str_replace("\\end{contentswolabwochp}", '', $content);

			$content = str_replace("\\begin{contentswochp}", '', $content);
			$content = str_replace("\\end{contentswochp}", '', $content);

			for ($i = 14; $i >= 0; $i--)
			{
				$insertion0 = $_POST["conweek" . $i];
				$insertion1 = $_POST["conchp" . $i];
				$insertion2 = $_POST["consub" . $i];
				$insertion3 = $_POST["conlab" . $i];

				$env = "contentswolab";
				$off = strlen($env) + 2;

				if (!empty($insertion2))
				{
					$pos = strpos($content, $env);
					$content = substr_replace($content, "\makeshortectsrow{" . $insertion0 . "}{" . $insertion1 . "}{" . $insertion2 . "}" . PHP_EOL , $pos+$off, 0);
				}
			}
		}
		else
		{
			$content = str_replace("\\begin{contents}", '', $content);
			$content = str_replace("\\end{contents}", '', $content);

			$content = str_replace("\\begin{contentswolab}", '', $content);
			$content = str_replace("\\end{contentswolab}", '', $content);

			$content = str_replace("\\begin{contentswochp}", '', $content);
			$content = str_replace("\\end{contentswochp}", '', $content);

			for ($i = 14; $i >= 0; $i--)
			{
				$insertion0 = $_POST["conweek" . $i];
				$insertion1 = $_POST["conchp" . $i];
				$insertion2 = $_POST["consub" . $i];
				$insertion3 = $_POST["conlab" . $i];

				$env = "contentswolabwochp";
				$off = strlen($env) + 2;

				if (!empty($insertion2))
				{
					$pos = strpos($content, $env);
					$content = substr_replace($content, "\makeveryshortectsrow{" . $insertion0 . "}{" . $insertion2 . "}" . PHP_EOL , $pos+$off, 0);
				}
			}
		}
	}


	file_put_contents($path_to_file, $content);

	if (PHP_OS === 'Linux')
	{
		exec('/usr/local/texlive/2021/bin/x86_64-linux/pdflatex syllabus.tex');
	}
	else
	{
		exec('C:\Users\orhan\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex.exe syllabus.tex');
	}

	$file = 'syllabus.pdf';

	header('Content-Type: application/pdf');
	header("Content-Disposition: inline; filename=\"$file\"");

	ob_clean();
	flush();

	readfile($file);
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
        if (!empty($_POST["obj" . $i])) {
            $objectives[] = $_POST["obj" . $i];
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
        if (!empty($_POST["source" . $i])) {
            $sources[] = $_POST["source" . $i];
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
        // if (!empty($_POST["consub" . $i])) {
            $conweeks[] = $_POST["conweek" . $i];
            $conchapters[] = $_POST["conchp" . $i];
            $consubjects[] = $_POST["consub" . $i];
            $conlabs[] = $_POST["conlab" . $i];
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
    array $rowStyle = [],
    array $paragraphStyle = []
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
        if (!empty($_POST["actper" . $i])) {
            $activities[] = $_POST["act" . $i];
            $activitiesper[] = $_POST["actper" . $i];
        }
    }

	

	foreach ($activities as $idx => $activity) {
		$table->addRow($rowStyle['height'] ?? null, $rowStyle);

		// $leftWidth = max(2000, strlen($activity) * 150);
		$table->addCell(2000)->addText($activity, [], $paragraphStyle);
		$table->addCell(9500)->addText($activitiesper[$idx], [], $paragraphStyle);
	}
}

function addOutcomesRow(
    \PhpOffice\PhpWord\Element\Table $table,
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
        if (!empty($_POST["obj" . $i])) {
            $outcomes0[] = $_POST["out" . $i];
            $outcomes1[] = $_POST["outval" . $i];
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

if (array_key_exists('submit_word', $_POST))
{
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

	addCourseRow($table, "Course Unit Title", $_POST['coursename'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Course Unit Code", $_POST['coursecode'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Type of Course Unit", $_POST['coursetype'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Level of Course Unit", "3rd Year BSc", $rowStyle, $paragraphStyle);
	addCourseRow($table, "National Credits", $_POST['nationalcredit'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Number of ECTS Credits Allocated", round(calculateEctsSum($_POST)/30), $rowStyle, $paragraphStyle);
	addCourseRow($table, "Theoretical (hour/week)", $_POST['theoretical'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Practice (hour/week)", "-", $rowStyle, $paragraphStyle);
	addCourseRow($table, "Laboratory (hour/week)", "-", $rowStyle, $paragraphStyle);
	addCourseRow($table, "Year of Study", "3", $rowStyle, $paragraphStyle);
	addCourseRow($table, "Semester when the course unit is delivered", "5", $rowStyle, $paragraphStyle);
	addCourseRow($table, "Mode of Delivery", "Face to face", $rowStyle, $paragraphStyle);
	addCourseRow($table, "Language of Instruction", "English", $rowStyle, $paragraphStyle);
	addCourseRow($table, "Prerequisites and co-requisites", $_POST['prerequisite'], $rowStyle, $paragraphStyle);
	addCourseRow($table, "Recommended Optional Programme Components", "An adequate background in calculus, physics, and engineering mechanics", $rowStyle, $paragraphStyle);

	addObjectivesRow($table, $rowStyleML, $paragraphStyle);

	$outcomestable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$contenttable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$sourcetable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
	$assesstable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

	addOutcomesRow($outcomestable, $rowStyle, $paragraphStyle);
	addContentsRow($contenttable, $rowStyle, $paragraphStyle);

	addSourcesRow($sourcetable, $rowStyleML, $paragraphStyle);


	// $tableStyle = [
    // 	'borderSize'  => 6,
    // 	'borderColor' => '000000',
    // 	// 'layout'      => 'autofit',
	// ];
	// $phpWord->addTableStyle('AssessmentTable', $tableStyle);
	// $assesstable = $section->addTable('AssessmentTable');


	addAssessmentRow($assesstable, $rowStyle, $paragraphStyle);

	






	











    $file = "syllabus.docx";
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($file);

	\PhpOffice\PhpWord\Settings::setPdfRenderer(
    \PhpOffice\PhpWord\Settings::PDF_RENDERER_TCPDF,
    __DIR__ . '/vendor/tecnickcom/tcpdf' // path to TCPDF in your project
	);

	// Save as PDF
	$filePDF = "syllabus.pdf";
	$writerPDF = IOFactory::createWriter($phpWord, 'PDF');
	$writerPDF->save($filePDF);

	

    // header("Content-Description: File Transfer");
    // header("Content-Disposition: attachment; filename=$file");
    // header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    // readfile($file);
    // unlink($file);
    // exit;

	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=syllabus.pdf");
	header("Content-Type: application/pdf");
	readfile($filePDF);
	unlink($filePDF);
	exit;
}






?>

<?php

require_once 'vendor/autoload.php';
require_once "functions.php";

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;





function generateSyllabusDocx($cleanPost, $tableWidth, $indent, $disheader)
{
    $phpWord = new PhpWord();

	$phpWord->setDefaultFontName('Calibri');
	$phpWord->setDefaultFontSize(10);

	$phpWord->setDefaultParagraphStyle([
    'spaceBefore' => 0,
    'spaceAfter'  => 0,
	'indentation' => [
        'left' => $indent
	],
	'spacing'     => 240, // 240 twips = 12pt = exactly 1 line
    'lineHeight'  => 1.0  // force single line
]);

	$sectionStyle = [
    'orientation' => 'portrait',
    'marginTop' => 1440,    // 1 inch = 1440 twips
    'marginBottom' => 1440,
    'marginLeft' => 1440,
    'marginRight' => 1440
];

	$section = $phpWord->addSection();

    // Add a footer to the section
    $footer = $section->addFooter();

    // Add page number
    $footer->addPreserveText(
    '{PAGE}', 
    null, // font style
    array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER) // paragraph style
    );

	// Header text
	$headerText = "GAU, Faculty of Engineering";
	$section->addText(
		$headerText,
		['bold' => true, 'size' => 14],               // font style: bold, size 16
		['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER] // paragraph style: centered
	);

	// Optional: add a line break after header
	$section->addTextBreak($disheader);

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

	$tableStyle = [
    'borderSize' => 6,
    'borderColor' => '000000',
    'layout' => 'fixed',
    'align' => 'center',
	'cellMarginTop' => 0,
    'cellMarginBottom' => 0,
    'cellMarginLeft' => 0,
    'cellMarginRight' => 0
];

    $table = $section->addTable($tableStyle);


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

	addCourseRow($table, "Course Unit Title", $cleanPost['coursename'], $tableWidth, $rowStyle);
	addCourseRow($table, "Course Unit Code", $cleanPost['coursecode'], $tableWidth, $rowStyle);
	addCourseRow($table, "Type of Course Unit", $cleanPost['coursetype'] . $eligdepString, $tableWidth, $rowStyle);

	addCourseRow($table, "Level of Course Unit", $cleanPost['level'], $tableWidth, $rowStyle);

	$natcre = floatval($cleanPost['theoretical']) + floatval($cleanPost['practice']) / 2 + + floatval($cleanPost['labcre']) / 2;

	addCourseRow($table, "National Credits", $natcre, $tableWidth, $rowStyle);


	addCourseRow($table, "Number of ECTS Credits Allocated", round(calculateEctsSum($cleanPost)/30), $tableWidth, $rowStyle);
	addCourseRow($table, "Theoretical (hour/week)", $cleanPost['theoretical'], $tableWidth, $rowStyle);
	addCourseRow($table, "Practice (hour/week)", $cleanPost['practice'], $tableWidth, $rowStyle);
	addCourseRow($table, "Laboratory (hour/week)", $cleanPost['labcre'], $tableWidth, $rowStyle);
	addCourseRow($table, "Year of Study", $cleanPost['yearofstudy'], $tableWidth, $rowStyle);
	addCourseRow($table, "Semester when the course unit is delivered", $cleanPost['semdel'], $tableWidth, $rowStyle);
	addCourseRow($table, "Mode of Delivery", $modeString, $tableWidth, $rowStyle);
	addCourseRow($table, "Language of Instruction", $cleanPost['lang'], $tableWidth, $rowStyle);
	addCourseRow($table, "Prerequisites and co-requisites", $cleanPost['prerequisite'], $tableWidth, $rowStyle);
	addCourseRow($table, "Recommended Optional Programme Components", $cleanPost['recom'], $tableWidth, $rowStyle);

	$objectivestable = $section->addTable($tableStyle);

	addObjectivesRow($objectivestable, $cleanPost, $tableWidth, $rowStyleML, $cleanPost);

	$outcomestable = $section->addTable($tableStyle);
	$contribstable = $section->addTable($tableStyle);
	$contenttable = $section->addTable($tableStyle);
	$sourcetable = $section->addTable($tableStyle);
	$assesstable = $section->addTable($tableStyle);
	$ectstable = $section->addTable($tableStyle);

	addOutcomesRow($outcomestable, $cleanPost, $tableWidth, $rowStyleML);
	addContribsRow($contribstable, $cleanPost, $tableWidth, $rowStyleML);
	addContentsRow($contenttable, $cleanPost, $tableWidth, $rowStyle);
	addSourcesRow($sourcetable, $cleanPost, $tableWidth, $rowStyleML);
	addAssessmentRow($assesstable, $cleanPost, $tableWidth, $rowStyle);
	addECTSRow($ectstable, $cleanPost, $tableWidth, $rowStyleML);

    // Step 1: Save DOCX to a temp file
	$tempDocx = tempnam(sys_get_temp_dir(), 'syllabus') . '.docx';
	$writer = IOFactory::createWriter($phpWord, 'Word2007');
	$writer->save($tempDocx);

    return $tempDocx;
}

if (array_key_exists('submit_pdf', $_POST))
{
	$cleanPost = fixPostAmpersands($_POST);
    $tempDocx = generateSyllabusDocx($cleanPost, 9500, 80, 1);

    $outputDir = sys_get_temp_dir();
    // $libreoffice = __DIR__ . "\\libre\\program\\soffice.exe";
    // $libreoffice = __DIR__ . "/libre/program/soffice.exe";

	if (stripos(PHP_OS, 'WIN') === 0) {
    $libreoffice = __DIR__ . "/libre/program/soffice.exe";
} else {
    $libreoffice = __DIR__ . "/libre/program/soffice";
}

echo "LibreOffice path: $libreoffice<br>";

if (!file_exists($libreoffice)) {
    die("Error: LibreOffice executable not found at $libreoffice");
}

if (!is_executable($libreoffice)) {
    die("Error: LibreOffice is not executable. Run chmod +x on $libreoffice");
}

echo "LibreOffice is ready to run ✅";

    $cmd = "\"$libreoffice\" --headless --convert-to pdf --outdir \"$outputDir\" \"$tempDocx\"";
    exec($cmd . " 2>&1", $output, $return_var);

    $generatedPdf = $outputDir . DIRECTORY_SEPARATOR . pathinfo($tempDocx, PATHINFO_FILENAME) . ".pdf";

    if (file_exists($generatedPdf)) {
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"syllabus.pdf\"");
        header("Content-Length: " . filesize($generatedPdf));
        flush();
        readfile($generatedPdf);
        unlink($generatedPdf);
    } else {
        echo "PDF conversion failed.";
    }

    unlink($tempDocx);
    exit;
}      

if (array_key_exists('submit_word', $_POST))
{
    $cleanPost = fixPostAmpersands($_POST);
    $tempDocx = generateSyllabusDocx($cleanPost, 9500, 80, 1);

    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    header("Content-Disposition: attachment; filename=\"syllabus.docx\"");
    header("Content-Length: " . filesize($tempDocx));
    flush();
    readfile($tempDocx);
    @unlink($tempDocx);
    exit;
}





?>

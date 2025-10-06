<?php

require_once 'vendor/autoload.php';
require_once "functions.php";

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;





function generateSyllabusDocx($cleanPost)
{
    // $cleanPost = $_POST;
	

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

    return $tempDocx;
}

if (array_key_exists('submit_pdf', $_POST))
{
	$cleanPost = fixPostAmpersands($_POST);
    $tempDocx = generateSyllabusDocx($cleanPost);

    $outputDir = sys_get_temp_dir();
    $libreoffice = __DIR__ . "\\libre\\program\\soffice.exe";
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
    $tempDocx = generateSyllabusDocx($cleanPost);

    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    header("Content-Disposition: attachment; filename=\"syllabus.docx\"");
    header("Content-Length: " . filesize($tempDocx));
    flush();
    readfile($tempDocx);
    @unlink($tempDocx);
    exit;
}





?>

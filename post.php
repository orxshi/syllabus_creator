<?php

require_once 'vendor/autoload.php';
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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




if (array_key_exists('submit_word', $_POST))
{
    $phpWord = new PhpWord();

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

	$paragraphStyle = [
		'spaceBefore' => 0,
		'spaceAfter' => 0,
	];

	$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']); // only style array here

	// First row
	$table->addRow($rowStyle['height'], $rowStyle);

	$labelText = "\u{00A0}\u{00A0}Course Unit Title";
	$table->addCell(
		3000,
		['valign' => 'center']
	)->addText($labelText, ['bold' => true], $paragraphStyle);

	$courseName = "\u{00A0}\u{00A0}" . ($_POST['coursename'] ?? 'Unknown Course Title');
	$table->addCell(
		6000,
		['valign' => 'center']
	)->addText($courseName, null, $paragraphStyle);

	// Second row
	$table->addRow($rowStyle['height'], $rowStyle);

	$labelText = "\u{00A0}\u{00A0}Course Unit Code";
	$table->addCell(
		3000,
		['valign' => 'center']
	)->addText($labelText, ['bold' => true], $paragraphStyle);

	$courseCode = "\u{00A0}\u{00A0}" . ($_POST['coursecode'] ?? 'Unknown Course Code');
	$table->addCell(
		6000,
		['valign' => 'center']
	)->addText($courseCode, null, $paragraphStyle);
    

    $file = "syllabus.docx";
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($file);

    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$file");
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    readfile($file);
    unlink($file);
    exit;
}






?>

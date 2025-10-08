# Syllabus creator

This is a course syllabus generator. The form collects information from the user and produces either PDF or docx file.

Open the docx file with LibreOffice not MS Word.

Usage of LaTeX for PDF generation is under development

![screenshot](img1.png)

## Motivation

* To have consistent format
* To avoid losing time with formatting
* To let national and ECTS credit to be calculated programatically to avoid mistakes

## Abilities

* Handles & and Microsoft-encoded quotes “ ” so you can just copy/paste from Word
* Handles Turkish characters

## Required packages

LibreOffice (install to the current folder with folder name libre)

## Dependencies

phpoffice/phpword which comes with the folder but if you wish you can install with ```composer require phpoffice/phpword```

## Optional packages

A LaTeX distribution

## Testing

The code has been tested with

* TeX Live 3.14.
* MixTeX 4.21.
* Ubuntu 19.04
* Windows 10
* LibreOffice 25.8.1

## Usage

* Open index.html
* Fill in form fields.
* Or click Load to load from JSON file. For convenience, MT111 is available.
* Click Generate PDF to generate PDF the syllabus.
* Click Generate docx to generate docx of the syllabus.
* Optionally, click Save to save form data.
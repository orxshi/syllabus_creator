\LoadClass[a4paper]{article}

\usepackage{tabularx}
\usepackage{booktabs}
\usepackage[left=2cm,right=2cm,top=2cm]{geometry}
%\usepackage{multirow}
\usepackage{graphicx}
\usepackage{needspace}

\setlength\parindent{0pt}
%\newcolumntype{s}{>{\hsize=.15\hsize}X}



\newcommand{\banner}
{
    \large
        \begin{minipage}[t]{0.15\textwidth}
    \raisebox{\dimexpr-\height+3.5ex\relax}{\includegraphics[scale=0.16]{logo}}
    \end{minipage}
    \begin{minipage}[t]{0.6\textwidth}
    {\LARGE\scshape Girne American University}\\[2mm]
    {\Large Faculty of Engineering}
    \end{minipage}
}

\newcommand{\syllabusTitle}
{
    \begin{center}
    \Large
        \@courseCode{} \@courseTitle\\[3mm]
        \large Syllabus
        \end{center}
}

\AtBeginDocument
{
    \banner
        \vspace{0.5cm}
    \syllabusTitle
        \vspace{0.5cm}

}

\newenvironment{textbook}
{
    \needspace{5cm}
    \textbf{Recommended sources}
    \begin{itemize}
}
{
    \end{itemize}
    \vspace{1cm}
}


\newcommand{\courseTitle}[1]{\gdef\@courseTitle{#1}}
\newcommand{\@courseTitle}{Course title}
\newcommand{\courseCode}[1]{\gdef\@courseCode{#1}}
\newcommand{\@courseCode}{Course code}
\newcommand{\courseType}[1]{\gdef\@courseType{#1}}
\newcommand{\@courseType}{Compulsory}
\newcommand{\courseLevel}[1]{\gdef\@courseLevel{#1}}
\newcommand{\@courseLevel}{Undergraduate 3rd year}
\newcommand{\nationalCredits}[1]{\gdef\@nationalCredits{#1}}
\newcommand{\@nationalCredits}{3}
\newcommand{\ECTS}[1]{\gdef\@ECTS{#1}}
\newcommand{\@ECTS}{3}
\newcommand{\theoretical}[1]{\gdef\@theoretical{#1}}
\newcommand{\@theoretical}{3}
\newcommand{\practical}[1]{\gdef\@practical{#1}}
\newcommand{\@practical}{3}
\newcommand{\laboratory}[1]{\gdef\@laboratory{#1}}
\newcommand{\@laboratory}{3}
\newcommand{\yearsOfStudy}[1]{\gdef\@yearsOfStudy{#1}}
\newcommand{\@yearsOfStudy}{3}
\newcommand{\semester}[1]{\gdef\@semester{#1}}
\newcommand{\@semester}{3}
\newcommand{\courseMode}[1]{\gdef\@courseMode{#1}}
\newcommand{\@courseMode}{3}
\newcommand{\instructionLanguage}[1]{\gdef\@instructionLanguage{#1}}
\newcommand{\@instructionLanguage}{3}
\newcommand{\prerequisite}[1]{\gdef\@prerequisite{#1}}
\newcommand{\@prerequisite}{3}
\newcommand{\recommended}[1]{\gdef\@recommended{#1}}
\newcommand{\@recommended}{3}


\makeatletter
\newcommand{\basic}
{
    %\begin{tabularx}{\textwidth}{@{}XX@{}}
    \begin{tabular}{@{}p{0.5\textwidth}p{0.45\textwidth}@{}}
    %Course unit title & \@courseTitle\\
        %Course unit code & \@courseCode\\
        Type of course unit & \@courseType\\
        Level of course unit & \@courseLevel\\
        National credits & \@nationalCredits\\
        Number of ECTS credits allocated & \@ECTS\\
        Theoretical (hour/week) & \@theoretical\\
        Practice (hour/week) & \@practical\\
        Laboratory (hour/week) & \@laboratory\\
        %Year of study & \@yearsOfStudy\\
        %Semester when the course unit is delivered & \@semester\\
        Mode of delivery & \@courseMode\\
        Language of instruction & \@instructionLanguage\\
        Prerequisities and co-requisities & \@prerequisite\\
        %Recommended optional programme components & \@recommended
        %\end{tabularx}
    \end{tabular}
}
\makeatother

\newenvironment{objectives}
{
    \needspace{10cm}
    %\begin{minipage}{\textwidth}
    \textbf{Objectives}
    \begin{itemize}
}
{
    \end{itemize}
    %\end{minipage}
    \vspace{1cm}
}

\newcommand{\makerow}[2]
{
#1 & #2\\
}

\newcommand{\makeectsrow}[4]
{
#1 & #2 & #3 & #4\\
}

\newcommand{\makeshortectsrow}[3]
{
#1 & #2 & #3\\
}

\newcommand{\makeveryshortectsrow}[2]
{
	#1 & #2\\
}

\newenvironment{outcomes}
{
    \needspace{10cm}
    \begin{minipage}{\textwidth}
    %\begin{samepage}
    \textbf{Learning outcomes}\\[3mm]
        (Assesment Methods: 1. Written Exam, 2. Assignment 3. Project/Report, 4.Presentation, 5 Lab. Work)\\[3mm]    

        %\tabularx{\textwidth}{@{}Xs@{}}
    \begin{tabular}{@{}p{0.8\textwidth}l@{}}
    Outcome & Assessment\\
        \midrule
}
{
    %\endtabularx    
        \end{tabular}
    \end{minipage}
    %\end{samepage}
    \vspace{1cm}
}

\newenvironment{assessment}
{
    \needspace{5cm}
    \begin{minipage}{\textwidth}
    \textbf{Assessment}\\[3mm]
        \begin{tabular}{ll}
    Activity & Weight (\%)\\
        \midrule
}
{
    \end{tabular}
    \end{minipage}
    \vspace{1cm}
}

\newenvironment{myects}
{
    \needspace{5cm}
    \begin{minipage}{\textwidth}
    \textbf{ECTS allocated based on the student workload}\\[3mm]
        \begin{tabular}{llll}
    Activity & Number of activity & Duration (hour) & Workload (hour)\\
        \midrule
}
{
    \end{tabular}
    \end{minipage}
    \vspace{1cm}
}

\newenvironment{contents}
{
    \needspace{10cm}
    \begin{minipage}{\textwidth}
    \textbf{Contents}\\[3mm]
    \begin{tabular}{llp{0.35\textwidth}p{0.35\textwidth}}
    Week & Chapter & Subject & Lab activity\\
        \midrule
}
{
    \end{tabular}
    \end{minipage}
    \vspace{1cm}
}

\newenvironment{contentswolab}
{
    \needspace{10cm}
    \begin{minipage}{\textwidth}
    \textbf{Contents}\\[3mm]
    \begin{tabular}{lll}
    Week & Chapter & Subject\\
        \midrule
}
{
    \end{tabular}
    \end{minipage}
    \vspace{1cm}
}

\newenvironment{contentswochp}
{
    \needspace{10cm}
    \begin{minipage}{\textwidth}
    \textbf{Contents}\\[3mm]
    \begin{tabular}{llp{0.35\textwidth}p{0.35\textwidth}}
    Week & Subject & Lab activity\\
        \midrule
}
{
    \end{tabular}
    \end{minipage}
    \vspace{1cm}
}

\newenvironment{contentswolabwochp}
{
    \needspace{10cm}
    \begin{minipage}{\textwidth}
    \textbf{Contents}\\[3mm]
    \begin{tabular}{lll}
    Week & Subject\\
        \midrule
}
{
    \end{tabular}
    \end{minipage}
    \vspace{1cm}
}





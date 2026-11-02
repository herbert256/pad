<?php

  // The format codes of TEXT, which formats= of {pad:spreadsheet} writes its columns with
  // (_lib/formulas.php): 0 # ? and the decimal point, a comma between digits for thousands
  // and after them for a thousandth, % and E+, sections for the negative numbers and 0,
  // texts in quotes, and the dates and times - an m after an h or before an s the minutes.

  $cases = formulasRun ( [
    '=TEXT(1234.567,"0")',
    '=TEXT(1234.567,"0.00")',
    '=TEXT(1234.567,"#,##0")',
    '=TEXT(1234.567,"#,##0.00")',
    '=TEXT(0.5,"0%")',
    '=TEXT(0.123,"0.0%")',
    '=TEXT(-1234.5,"#,##0.00")',
    '=TEXT(-1234.5,"#,##0.00;(#,##0.00)")',
    '=TEXT(0,"0.00;-0.00;""zero""")',
    '=TEXT(12345678,"#,##0,")',
    '=TEXT(1234567,"0.0,,")',
    '=TEXT(0.00001234,"0.00E+00")',
    '=TEXT(123456,"0.0E+0")',
    '=TEXT(5,"#.##")',
    '=TEXT(0.5,"#.00")',
    '=TEXT(3,"000")',
    '=TEXT(3.14159,"?.???")',
    '=TEXT(1234.5,"$#,##0.00")',
    '=TEXT(1234.5,"€ #,##0.00")',
    '=TEXT(1234.5,"0.00 ""EUR""")',
    '=TEXT(45306,"yyyy-mm-dd")',
    '=TEXT(45306,"d/m/yy")',
    '=TEXT(45306,"mmm d, yyyy")',
    '=TEXT(45306,"dddd")',
    '=TEXT(45306,"ddd dd mmmm")',
    '=TEXT(45306,"mmmmm")',
    '=TEXT(45306.75,"yyyy-mm-dd hh:mm:ss")',
    '=TEXT(0.75,"h:mm AM/PM")',
    '=TEXT(0.25,"hh:mm a/p")',
    '=TEXT(1.5,"[h]:mm")',
    '=TEXT(15/1440,"mm:ss")',
    '=TEXT(1234.5,"General")',
    '=TEXT(-1,"yyyy")',
    '=TEXT(TRUE,"0")',
    '=TEXT("x","""<""@"">""")'
  ] );

?>

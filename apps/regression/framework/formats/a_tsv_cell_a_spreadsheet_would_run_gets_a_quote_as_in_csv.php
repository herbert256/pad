<?php

  // A spreadsheet opens a .tsv as it opens a .csv and runs a cell that starts with =, +, -
  // or @: the TSV writer gives such a text cell - and a header name - the ' in front the CSV
  // writer gives it, where it wrote a visitor's =HYPERLINK(...) live, one padFormat= away
  // from the CSV answer that defused it; a number of either sign stays as it is.

  $tsvRows = [ [ 'name' => '=HYPERLINK("http://evil.example","click")', 'cmd' => "+cmd|' /C calc'!A0", 'at' => '@SUM(A1)', 'minus' => '-2+3', 'n' => -5, '=x' => 1.5 ] ];

  $tsvShow = str_replace ( [ "\t", "\n" ], [ '|', '~' ], padFormatWrite ( 'tsv', $tsvRows ) );
  $csvShow = str_replace ( "\n", '~', padFormatWrite ( 'csv', $tsvRows ) );

?>

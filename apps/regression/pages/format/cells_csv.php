<?php

  // A CSV answer is for a spreadsheet, which runs a cell that starts with =, +, - or @ as a
  // formula: a visitor's =HYPERLINK(...) or +cmd|... in the data became a link or a command
  // in the spreadsheet of whoever opened the export. Such a text cell is written with a '
  // in front, which a spreadsheet shows as the text it is; a number of either sign, and text
  // that only holds those characters further on, are written as they are.

  $cellsCsv = padCurl ( $padGoExt . 'format/cells&padFormat=csv' );

  echo str_replace ( "\n", ' | ', trim ( $cellsCsv ['data'] ) );

?>

<?php

  // A Markdown pipe table: the first table of a text, the text around it left, \| a pipe in a
  // cell and <br> a line break. Written, the columns are lined up - a column of numbers on
  // the right, its row of dashes ending in : - a | written \| and a newline <br>.

  $mdText = "# Scores\n\nThe table is the data.\n\n| player | note | points |\n|:--|---|--:|\n| Ann | a \\| b | 21 |\n| Bob | one<br>two | 7 |\n\nAfter the table.";
  $mdRows = padFormatRead ( 'md', $mdText );

  $mdJson = json_encode ( $mdRows );
  $mdShow = str_replace ( "\n", ' / ', trim ( padFormatWrite ( 'markdown', $mdRows ) ) );

?>

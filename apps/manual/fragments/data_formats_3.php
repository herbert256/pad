<?php

  $readme = "# Scores\n\nThe table is the data.\n\n| player | points |\n| ------ | -----: |\n| Ann    |     21 |\n| Bob    |     17 |\n";

  $scores = padFormatRead ( 'markdown', $readme );
  $tsv    = padFormatWrite ( 'tsv', $scores );

?>

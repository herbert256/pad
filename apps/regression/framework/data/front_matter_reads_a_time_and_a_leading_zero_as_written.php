<?php

  // A time of day and a number with a leading 0 are the text written in front matter, with
  // the yaml extension and without it - libyaml read 9:30 as 570 and 0123 as octal 83.

  $yaml  = "time: 9:30\nzip: 0123\ncount: 3\nprice: 2.5";

  $lines = json_encode ( padFrontMatterLines ( $yaml ) );
  $read  = json_encode ( padFrontMatterRead ( $yaml, 'post.md' ) );

?>

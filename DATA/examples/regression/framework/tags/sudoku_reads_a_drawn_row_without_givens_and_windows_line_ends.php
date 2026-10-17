<?php

  // A row with no given written as dashes with spaces or bars between them is nine empty
  // cells, not a line between bands, and a grid with the \r\n line ends of a posted
  // textarea passes over its band lines as well: the first puzzle had 72 cells, the
  // second 99.

  $spaced = "- - - | - - - | - - -\n"
          . "6 - - | 1 9 5 | - - -\n"
          . "- 9 8 | - - - | - 6 -\n"
          . "8 - - | - 6 - | - - 3\n"
          . "4 - - | 8 - 3 | - - 1\n"
          . "7 - - | - 2 - | - - 6\n"
          . "- 6 - | - - - | 2 8 -\n"
          . "---|---|---\n"
          . "- - - - 8 - - 7 9\n";

  $posted = str_replace ( "\n", "\r\n",
              "5 3 . | . 7 . | . . .\n6 . . | 1 9 5 | . . .\n. 9 8 | . . . | . 6 .\n------+-------+------\n"
            . "8 . . | . 6 . | . . 3\n4 . . | 8 . 3 | . . 1\n7 . . | . 2 . | . . 6\n------+-------+------\n"
            . ". 6 . | . . . | 2 8 .\n. . . | 4 1 9 | . . 5\n. . . | . 8 . | . 7 9\n" );

?>

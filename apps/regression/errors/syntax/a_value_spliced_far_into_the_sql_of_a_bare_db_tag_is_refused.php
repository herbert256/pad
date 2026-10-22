<?php

  // A value spliced more than 4 KB past the { of {db} - beyond the window the splice looks
  // back over - went in bare, and this one counted every row: the tag judges it itself, as a
  // database tag does.

  $max = '0 or 1';

?>

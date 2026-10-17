<?php

  // A value spliced into a where= more than 4 KB from the tag's opening brace - past the window
  // the splice looks back over - is refused as part of the condition: it went in bare and
  // unrecorded, and the request value 3 or php:abs(-3) eq 3 ran PHP and let every row pass.

  $q = '3 or php:abs(-3) eq 3';

?>

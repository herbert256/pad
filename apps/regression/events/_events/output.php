<?php

  // Sees each finished page of this application before it goes out: the marker becomes the
  // proof that it ran.

  $output = str_replace ( '[[output-hook]]', 'the output hook of the root ran', $output );

?>

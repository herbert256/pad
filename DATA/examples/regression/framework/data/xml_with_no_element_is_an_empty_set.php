<?php

  // An XML document that holds no element - only a prolog, or only a comment - parses to no
  // rows. It ended the request on a foreach() over FALSE from inside the reader.

  $prolog  = '<?xml version="1.0"?>';
  $comment = '<!-- nothing here -->';

?>

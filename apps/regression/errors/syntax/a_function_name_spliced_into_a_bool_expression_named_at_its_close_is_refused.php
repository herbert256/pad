<?php

  // A single word spliced into the expression of a {bool} named on its closing tag is no
  // string when the evaluator knows it as a reference: the request value connection_status
  // called that PHP function with no arguments, and its 0 made the comparison TRUE. A word
  // that names nothing, {$s} eq 'paid', still compares.

  $w = 'connection_status';

?>

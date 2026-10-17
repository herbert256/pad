<?php

  // A value spliced into the quoted expression of the eval sequence is a string of it in every
  // spelling - {eval '...'}, {sequence:eval '...'}, make='eval|...', sequence:eval(2, '...') -
  // as in eval='...': only eval= quoted it, and in the others the value became part of the
  // expression and called the function it named.

  $q = 'php:gettype()';

?>

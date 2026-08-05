<?php

  // Marker file, never included: its existence is what makes 'sole' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // sole=N collapses the range onto that one value - inits/set.php sets both $pqFrom and
  // $pqTo to it, overriding from= and to= - so the build weighs exactly one candidate and
  // the run answers "does N belong to this sequence". inits/parms.php reads it into $pqSole.

  return TRUE;

?>
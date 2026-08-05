<?php

  // Marker file, never included: its existence is what makes 'name' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // name='var' is the variable each term is published under inside the tag pair, so
  // {sequence 5, name='i'} makes {$i} work. inits/parms.php reads it into $pqName and
  // inits/set.php keeps the given form in $pqNameGiven; inits/name.php falls back to the
  // push/pull store name, then the sequence type, then 'sequence', and exits/return/ binds
  // the rows under it. It is also the first choice of store name in exits/store/last.php.

  return TRUE;

?>
<?php

  // Pipe function capitalize: upper-cases the first letter of every word, leaving the rest
  // of each word alone; ucwords is the same function under its PHP name. In UTF-8, so éric
  // becomes Éric - PHP's ucwords knows ASCII only. Words end at whitespace, as ucwords has
  // them.

  return preg_replace_callback ( '/(^|\s)(\X)/u', fn ( $m ) => $m [1] . mb_strtoupper ( $m [2], 'UTF-8' ), (string) $value );

?>

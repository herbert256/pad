<?php

  // array OP array: only equality and inequality are supported, comparing the two arrays whole
  // and yielding PAD's string booleans. Any other operator raises a 'ToDo' error.

  if     ( $opr == 'EQ'  ) $now = ($left == $right) ? 1 : '';
  elseif ( $opr == 'NE'  ) $now = ($left != $right) ? 1 : '';

  else padError ( 'ToDo' );

?>
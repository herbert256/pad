<?php

  // A record whose every node is a variation of the one before, 50 000 deep: the parser
  // called itself once per ( and the page ended with PHP's "Maximum call stack size reached".

  $record = '(;SZ[9](;B[ee]' . str_repeat ( '(;', 50000 ) . str_repeat ( ')', 50001 ) . ')';

?>

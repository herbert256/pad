<?php

  // Reached when the tag named both a stored sequence and a sequence type, as in
  // {pull:mySeq, prime}. The type is not what gets generated - the store is - so the type is
  // demoted to a play over the pulled values by plays/add.php, and $pqSeq / $pqParm are
  // cleared so build/inits.php settles on the pull.

  $padPrmName  = $pqSeq;
  $padPrmValue = $pqParm;

  include PQ . 'plays/add.php';

  $pqSeq  = '';
  $pqParm = '';

?>
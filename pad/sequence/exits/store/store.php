<?php

  if ( $padPair [$pad] and ! $pqPush )
    return;

  include PQ . 'exits/store/last.php';
  include PQ . 'exits/store/check.php';
  include PQ . 'exits/store/set.php';

?>

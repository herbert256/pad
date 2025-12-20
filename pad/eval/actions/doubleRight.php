<?php

  $left = $result [$f] [0];
  $right = $myself;

  unset ( $result [$f] ); padEvalTrace ( 'action/doubleright', $result );

  include PAD . 'eval/go/go.php';

?>

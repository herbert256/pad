<?php
  
  $left = $result [$f] [0];
  $right = $myself;

  unset ( $result [$f] ); padEvalTrace ( 'action/doubleright', $result );

  include 'eval/go/go.php';

?>
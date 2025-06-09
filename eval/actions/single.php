<?php
  
  $right = $result [$k] [0];

  unset ( $result [$k] ); padEvalTrace ( 'action/single', $result );

  include 'eval/go/go.php';
  
?>
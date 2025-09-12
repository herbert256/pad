<?php
  
  $left  = $myself;
  $right = $result [$k] [0];

  unset ( $result [$k] ); padEvalTrace ( 'action/doubleleft', $result );

  include 'eval/go/go.php';

?>
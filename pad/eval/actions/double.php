<?php
  
  $left = $result [$f] [0];
  $right = $result [$k] [0];

  unset ( $result [$f] );
  unset ( $result [$k] ); padEvalTrace ( 'action/double', $result );

  include 'eval/go/go.php';

?>
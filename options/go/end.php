<?php

  $padOptions = 'end';
  include "options.php";

  foreach ( $padEndOptions[$pad] as $padOptionName ) {

    include PAD . "options/end/$padOptionName.php" ;

    if ($padTrace)
      include 'trace.php';

  }
  
?>
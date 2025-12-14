<?php

  if ( $pqLoop == 1 ) return 1;
  if ( $pqLoop == 2 ) return 3;

  return include PT . "fibonacci/go.php";

?>
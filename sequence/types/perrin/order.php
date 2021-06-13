<?php

  if ( $n == 1 ) return 3;
  if ( $n == 2 ) return 0; 
  if ( $n == 3 ) return 2; 

  return include PAD_HOME . "fibonacci/fibonacci.php"; 

?>
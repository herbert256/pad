<?php

  if ( $pqLoop == 1 ) return 0;
  if ( $pqLoop == 2 ) return 1; 

  return include PT . 'fibonacci/go.php'; 

?>
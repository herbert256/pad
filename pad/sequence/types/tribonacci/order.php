<?php

  if ( $pqLoop == 1 ) return 0;
  if ( $pqLoop == 2 ) return 0;
  if ( $pqLoop == 3 ) return 1;

  return $pqOrder [$pqLoop - 2] +
         $pqOrder [$pqLoop - 3] +
         $pqOrder [$pqLoop - 4];

?>
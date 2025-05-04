<?php

  if ( $pqLoop == 1 ) return 1;

  return 1 + $pqOrder [ $pqLoop - ($pqOrder [ $pqOrder [ $pqLoop - 2 ] - 1 ] + 1) ];

?>
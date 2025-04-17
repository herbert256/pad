<?php

  if ( $pqLoop == 1 ) return 1;

  return 1 + $pqResult [ $pqLoop - ($pqResult [ $pqResult [ $pqLoop - 2 ] - 1 ] + 1) ];

?>
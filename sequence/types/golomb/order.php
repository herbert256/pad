<?php

  if ( $padSeqLoop == 1 ) return 1;

  return 1 + $padSeqResult [ $padSeqLoop - ($padSeqResult [ $padSeqResult [ $padSeqLoop - 2 ] - 1 ] + 1) ];

?>
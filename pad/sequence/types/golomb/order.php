<?php

  if ( $padSeq_loop == 1 ) return 1;

  return 1 + $padSeq_result [ $padSeq_loop - ($padSeq_result [ $padSeq_result [ $padSeq_loop - 2 ] - 1 ] + 1) ];

?>

<?php

  if ( $pSeq_loop == 1 ) return 1;

  return 1 + $pSeq_result [ $pSeq_loop - ($pSeq_result [ $pSeq_result [ $pSeq_loop - 2 ] - 1 ] + 1) ];

?>

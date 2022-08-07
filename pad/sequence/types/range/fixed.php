<?php

  $pSeq_range_parms = pExplode ( $pSeq_parm, '..' );

  return range ( $pSeq_range_parms[0], $pSeq_range_parms[1], $pSeq_inc );

?>
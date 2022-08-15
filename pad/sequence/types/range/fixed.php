<?php

  $padSeq_range_parms = padExplode ( $padSeq_parm, '..' );

  return range ( $padSeq_range_parms[0], $padSeq_range_parms[1], $padSeq_inc );

?>
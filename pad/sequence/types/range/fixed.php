<?php

  $pad_seq_range_parms = pad_explode ( $pad_seq_parm, '..' );

  return range ( $pad_seq_range_parms[0], $pad_seq_range_parms[1], $pad_seq_inc );

?>
<?php

  $pad_seq_range_parms = pad_explode ( $pad_seq_parm, '..' );
  $pad_seq_range_arr   = range ( $pad_seq_range_parms[0], $pad_seq_range_parms[1], $pad_seq_increment );

  return $pad_seq_range_arr;

?>
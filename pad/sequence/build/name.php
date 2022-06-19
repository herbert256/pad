<?php

  if     ( $pad_seq_name                            ) $pad_seq_name = $pad_seq_name;
  elseif ( $pad_seq_to_data                         ) $pad_seq_name = $pad_seq_to_data;
  elseif ( $pad_seq_push and $pad_seq_push !== TRUE ) $pad_seq_name = $pad_seq_push;
  else                                                $pad_seq_name = $pad_seq_set; 

  $pad_name = $pad_parameters [$pad_lvl] ['name'] = $pad_seq_name;

?>
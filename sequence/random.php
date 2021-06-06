<?php

  pad_set_arr_var ( 'options_done', 'random', TRUE );

  if ( $pad_tag == 'step' )
    return pad_random_one_step  ( $pad_seq_min, $pad_seq_max, $pad_seq_step, $pad_seq_floor, $pad_seq_ceil );

  if ( $pad_tag == 'multiple' )
    return pad_random_one_multi ( $pad_seq_min, $pad_seq_max, $pad_seq_multiple, $pad_seq_floor, $pad_seq_ceil );

  if ( $pad_tag == 'prime' )
    return pad_random_one_prime ( $pad_seq_min, $pad_seq_max );

  if ( $pad_tag == 'power' )
    return pad_random_one_power ( $pad_seq_min, $pad_seq_max, $pad_seq_power );

?>
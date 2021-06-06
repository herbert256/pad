<?php

  include_once PAD_HOME . 'tags/lib/random.php';

  $pad_random_count = $pad_parms_tag ['count']    ?? $pad_parm;
  $pad_random_min   = $pad_parms_tag ['min']      ?? 0;
  $pad_random_max   = $pad_parms_tag ['max']      ?? 0;
  $pad_random_step  = $pad_parms_tag ['step']     ?? 0;
  $pad_random_multi = $pad_parms_tag ['multiple'] ?? 0;
  $pad_random_prime = $pad_parms_tag ['prime']    ?? 0;
  $pad_random_power = $pad_parms_tag ['power']    ?? 0;
  $pad_random_floor = $pad_parms_tag ['floor']    ?? 0;
  $pad_random_ceil  = $pad_parms_tag ['ceil']     ?? 0;
  $pad_random_uniq  = $pad_parms_tag ['unique']   ?? 0;

  pad_min_max_count ( $pad_random_min, $pad_random_max, $pad_random_count );

  if ( $pad_random_uniq )
    return pad_random_unique ($pad_random_min, $pad_random_max, $pad_random_step, $pad_random_multi, $pad_random_count, $pad_random_floor, $pad_random_ceil, $pad_random_prime, $pad_random_power);
  else
    return pad_random        ($pad_random_min, $pad_random_max, $pad_random_step, $pad_random_multi, $pad_random_count, $pad_random_floor, $pad_random_ceil, $pad_random_prime, $pad_random_power);

?>
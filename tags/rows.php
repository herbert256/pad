<?php

  include_once PAD_HOME . 'tags/lib/sequence.php';

  $pad_seq_count  = $pad_parms_tag ['count']     ?? $pad_parm;
  $pad_seq_min    = $pad_parms_tag ['min']       ?? 0;
  $pad_seq_max    = $pad_parms_tag ['max']       ?? 0;
  $pad_seq_start  = $pad_parms_tag ['start']     ?? 0;
  $pad_seq_end    = $pad_parms_tag ['end']       ?? 0;
  $pad_seq_multi  = $pad_parms_tag ['multiple']  ?? 0;
  $pad_seq_prime  = $pad_parms_tag ['prime']     ?? 0;
  $pad_seq_power  = $pad_parms_tag ['power']     ?? 0;
  $pad_seq_step   = $pad_parms_tag ['step']      ?? 1;
  $pad_seq_fibo   = $pad_parms_tag ['fibonacci'] ?? 0;
  $pad_seq_lucas  = $pad_parms_tag ['lucas']     ?? 0;
  $pad_seq_pado   = $pad_parms_tag ['padovan']   ?? 0;
  $pad_seq_perrin = $pad_parms_tag ['perrin']    ?? 0;
  $pad_seq_golomb = $pad_parms_tag ['golomb']    ?? 0;

  pad_min_max_count ( $pad_seq_min, $pad_seq_max, $pad_seq_count );

  $pad_seq = $pad_wrk = [];

  $pad_seq_idx = 1;

  if ( $pad_seq_fibo ) {

    pad_seq ( 0, $pad_seq_min, $pad_wrk, $pad_seq, $pad_seq_idx );

    $pad_seq_now = 1;
 
  } elseif ( $pad_seq_lucas ) {

    pad_seq ( 1, $pad_seq_min, $pad_wrk, $pad_seq, $pad_seq_idx );

    $pad_seq_now = 3;

  } elseif ( $pad_seq_pado ) {

    pad_seq ( 1, $pad_seq_min, $pad_wrk, $pad_seq, $pad_seq_idx );
    pad_seq ( 1, $pad_seq_min, $pad_wrk, $pad_seq, $pad_seq_idx );

    $pad_seq_now  = 1;

  } elseif ( $pad_seq_perrin ) {

    pad_seq ( 3, $pad_seq_min, $pad_wrk, $pad_seq, $pad_seq_idx );
    pad_seq ( 0, $pad_seq_min, $pad_wrk, $pad_seq, $pad_seq_idx );

    $pad_seq_now = 2;

  } elseif ( $pad_seq_golomb ) {

    pad_seq ( 1, $pad_seq_min, $pad_wrk, $pad_seq, $pad_seq_idx );

    $pad_seq_now = 2;

  } elseif ( $pad_seq_multi ) {

    $pad_seq_now = ceil ( $pad_seq_min / $pad_seq_multi ) * $pad_seq_multi;

  } elseif ( $pad_seq_prime ) {

    if ( ! gmp_prob_prime ($pad_seq_min) == 2 )
      $pad_seq_now = gmp_intval ( gmp_nextprime ($pad_seq_min) );

  } elseif ( $pad_seq_power ) {

    for ($pad_seq_idx=1; $pad_seq_idx <= 64; $pad_seq_idx++) {
      $pad_seq_now = $pad_seq_power**$pad_seq_idx;
      if ($pad_seq_now >= $pad_seq_min)
        break;
    }

  } else {

    $pad_seq_now = $pad_seq_min;

  }

  while ( ($pad_seq_count > count ($pad_seq)) and ($pad_seq_now <= $pad_seq_max) ) {

    $pad_wrk [$pad_seq_idx] = $pad_seq_now;

    if ( $pad_seq_now >= $pad_seq_min )
      $pad_seq [$pad_seq_idx] = $pad_seq_now;

    if     ( $pad_seq_fibo   ) $pad_seq_now = $pad_wrk [$pad_seq_idx]   + $pad_wrk [$pad_seq_idx-1];
    elseif ( $pad_seq_lucas  ) $pad_seq_now = $pad_wrk [$pad_seq_idx]   + $pad_wrk [$pad_seq_idx-1];
    elseif ( $pad_seq_pado   ) $pad_seq_now = $pad_wrk [$pad_seq_idx-1] + $pad_wrk [$pad_seq_idx-2];
    elseif ( $pad_seq_perrin ) $pad_seq_now = $pad_wrk [$pad_seq_idx-1] + $pad_wrk [$pad_seq_idx-2];
    elseif ( $pad_seq_golomb ) $pad_seq_now = 1 + $pad_wrk[$pad_seq_idx + 1 - $pad_wrk[$pad_wrk[$pad_seq_idx]]];
    elseif ( $pad_seq_multi  ) $pad_seq_now = $pad_seq_now + $pad_seq_multi;
    elseif ( $pad_seq_prime  ) $pad_seq_now = gmp_intval ( gmp_nextprime ($pad_seq_now) );
    elseif ( $pad_seq_power  ) $pad_seq_now = $pad_seq_power**$pad_seq_idx;
    else                       $pad_seq_now = $pad_seq_now + $pad_seq_step;

    $pad_seq_idx++;

  }

  return $pad_seq; 

?>
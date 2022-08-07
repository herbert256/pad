<?php

  $pSeq_init = TRUE;

  if ( count ($pSeq_for) and is_array(reset($pSeq_for)) )
    foreach ( $pSeq_for as $pK => $pV )
      $pSeq_for [$pK] = reset($pV);

  foreach ( $pSeq_for as $pSeq_loop ) {

    if ( $pSeq_random )
      $pSeq_loop = $pSeq_for [array_rand($pSeq_for)] ;

    if ( ! include 'go/one.php')
        break;

   $pSeq_init = FALSE;

  }

?>
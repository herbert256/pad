<?php

  $padSeq_init = TRUE;

  if ( count ($padSeq_for) and is_array(reset($padSeq_for)) )
    foreach ( $padSeq_for as $padK => $padV )
      $padSeq_for [$padK] = reset($padV);

  foreach ( $padSeq_for as $padSeq_loop ) {

    if ( $padSeq_random )
      $padSeq_loop = $padSeq_for [array_rand($padSeq_for)] ;

    if ( ! include 'go/one.php')
        break;

   $padSeq_init = FALSE;

  }

?>
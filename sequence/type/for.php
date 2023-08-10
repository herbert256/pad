<?php

  if ( count ($padSeqFor) and is_array(reset($padSeqFor)) )
    foreach ( $padSeqFor as $padK => $padV )
      $padSeqFor [$padK] = reset($padV);

  foreach ( $padSeqFor as $padSeqLoop ) {

    if ( $padSeqRandom )
      $padSeqLoop = $padSeqFor [array_rand($padSeqFor)] ;

    if ( ! include pad . 'sequence/type/go/one.php')
        break;

  }
 
?>
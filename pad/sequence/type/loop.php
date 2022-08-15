  <?php

  include PAD . "sequence/build/save.php";

  $padSeq_init = TRUE;

  $padSeq_go = $padSeq_loop_start;

  while ( $padSeq_go <= $padSeq_loop_end ) {

    if ( ! $padSeq_random )

      $padSeq_loop = $padSeq_go;

    elseif ( $padSeq_inc == 1)

      $padSeq_loop = pSeq_random ( $padSeq_loop_start, $padSeq_loop_end );

    else {

      $padSeq_incCnt = round ( (($padSeq_loop_end-$padSeq_loop_start)+1) / $padSeq_inc );
      $padSeq_incCnt = pSeq_random ( 0, $padSeq_incCnt );

      $padSeq_loop = $padSeq_loop_start + ($padSeq_incCnt*$padSeq_inc);

    }

    if ( ! include 'go/one.php')
        break;

    $padSeq_init = FALSE;

    $padSeq_go = $padSeq_go + $padSeq_inc;

  }

?>
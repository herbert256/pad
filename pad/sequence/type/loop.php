  <?php

  include PAD . "sequence/build/save.php";

  $pSeq_init = TRUE;

  $pSeq_go = $pSeq_loop_start;

  while ( $pSeq_go <= $pSeq_loop_end ) {

    if ( ! $pSeq_random )

      $pSeq_loop = $pSeq_go;

    elseif ( $pSeq_inc == 1)

      $pSeq_loop = pSeq_random ( $pSeq_loop_start, $pSeq_loop_end );

    else {

      $pSeq_incCnt = round ( (($pSeq_loop_end-$pSeq_loop_start)+1) / $pSeq_inc );
      $pSeq_incCnt = pSeq_random ( 0, $pSeq_incCnt );

      $pSeq_loop = $pSeq_loop_start + ($pSeq_incCnt*$pSeq_inc);

    }

    if ( ! include 'go/one.php')
        break;

    $pSeq_init = FALSE;

    $pSeq_go = $pSeq_go + $pSeq_inc;

  }

?>
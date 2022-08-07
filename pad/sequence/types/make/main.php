<?php

  $GLOBALS ["pSeq_mf_$pSeq_seq"] = [];
 
  foreach ( $pPrmsTag [$p] as $pSeq_opt_name => $pSeq_opt_value )

    if ( file_exists ( PAD . "sequence/types/$pSeq_opt_name/$pSeq_filter_check.php" ) ) {

      $GLOBALS ["pSeq_mf_$pSeq_seq"] [$pSeq_opt_name] = $pSeq_opt_value;

      pDone ( $pSeq_opt_name, TRUE );

      $GLOBALS ["pSeq_$pSeq_opt_name"] = $pSeq_opt_value;

    }
   
  $pSequenceStore_get = $pSeq_parm;
  $pSeq_for = $pSequenceStore [$pSeq_parm];

  if ( ! $pSeq_push and ! $pPair )
    $pSeq_push = $pSeq_parm;

  include PAD . "sequence/type/for.php";
      
?>
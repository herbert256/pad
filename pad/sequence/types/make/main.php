<?php

  $GLOBALS ["padSeq_mf_$padSeq_seq"] = [];
 
  foreach ( $padPrmsTag [$pad] as $padSeq_opt_name => $padSeq_opt_value )

    if ( file_exists ( PAD . "sequence/types/$padSeq_opt_name/$padSeq_filter_check.php" ) ) {

      $GLOBALS ["padSeq_mf_$padSeq_seq"] [$padSeq_opt_name] = $padSeq_opt_value;

      pDone ( $padSeq_opt_name, TRUE );

      $GLOBALS ["padSeq_$padSeq_opt_name"] = $padSeq_opt_value;

    }
   
  $padSequenceStore_get = $padSeq_parm;
  $padSeq_for = $padSequenceStore [$padSeq_parm];

  if ( ! $padSeq_push and ! $padPair )
    $padSeq_push = $padSeq_parm;

  include PAD . "sequence/type/for.php";
      
?>
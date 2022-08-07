<?php

  $GLOBALS ["pSeq_$pSeq_one" . "_list"] = [];

  if ( $pSeq_one == $pSeq_seq ) 
    return;

  if ( ! isset($pPrmsTag [$p] [$pSeq_one]) )
    return;

  $GLOBALS["pSeq_".$pSeq_one. "_list"] = [];

  $pSeq_one_tmp = pExplode ( $pPrmsTag [$p] [$pSeq_one], ';');

  $pSeq_one_list = [];
  foreach ( $pSeq_one_tmp as $pEntry )
    $pSeq_one_list [$pEntry ] = TRUE;

  foreach ( $pSeq_one_list as $pSeq_one_name => $pSeq_one_value )

    if ($pSeq_one_name <> $pSeq_seq and file_exists (PAD . "sequence/types/$pSeq_one_name/$pSeq_filter_check.php")) {

      $GLOBALS["pSeq_".$pSeq_one. "_list"] [$pSeq_one_name] = $pSeq_one_value;

      pDone ( $pSeq_one_name, TRUE );

      $GLOBALS ["pSeq_$pSeq_one_name"] = $pSeq_one_value;

    }
  
?>
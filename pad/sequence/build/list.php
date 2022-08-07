<?php

  $GLOBALS ["pSeq_$pSeq_one" . "_list"] = [];

  if ( $pSeq_one == $pSeq_seq ) 
    return;

  if ( ! isset($pPrms_tag [$pSeq_one]) )
    return;

  $GLOBALS["pSeq_".$pSeq_one. "_list"] = [];

  $pSeq_one_tmp = pExplode ( $pPrms_tag [$pSeq_one], ';');

  $pSeq_one_list = [];
  foreach ( $pSeq_one_tmp as $pPrms_tag_entry )
    $pSeq_one_list [$pPrms_tag_entry ] = TRUE;

  foreach ( $pSeq_one_list as $pSeq_one_name => $pSeq_one_value )

    if ($pSeq_one_name <> $pSeq_seq and file_exists (PAD . "sequence/types/$pSeq_one_name/$pSeq_filter_check.php")) {

      $GLOBALS["pSeq_".$pSeq_one. "_list"] [$pSeq_one_name] = $pSeq_one_value;

      pDone ( $pSeq_one_name, TRUE );

      $GLOBALS ["pSeq_$pSeq_one_name"] = $pSeq_one_value;

    }
  
?>
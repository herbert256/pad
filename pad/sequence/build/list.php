<?php

  $GLOBALS ["padSeq_$padSeq_one" . "_list"] = [];

  if ( $padSeq_one == $padSeq_seq ) 
    return;

  if ( ! isset($padPrmsTag [$pad] [$padSeq_one]) )
    return;

  $GLOBALS["padSeq_".$padSeq_one. "_list"] = [];

  $padSeq_one_tmp = padExplode ( $padPrmsTag [$pad] [$padSeq_one], ';');

  $padSeq_one_list = [];
  foreach ( $padSeq_one_tmp as $padEntry )
    $padSeq_one_list [$padEntry ] = TRUE;

  foreach ( $padSeq_one_list as $padSeq_one_name => $padSeq_one_value )

    if ($padSeq_one_name <> $padSeq_seq and file_exists (PAD . "sequence/types/$padSeq_one_name/$padSeq_filter_check.php")) {

      $GLOBALS["padSeq_".$padSeq_one. "_list"] [$padSeq_one_name] = $padSeq_one_value;

      padDone ( $padSeq_one_name, TRUE );

      $GLOBALS ["padSeq_$padSeq_one_name"] = $padSeq_one_value;

    }
  
?>
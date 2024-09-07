<?php

  foreach ( $padSeqActionList as $padSeqMergeKey ) {

    $padSeqMerge1 = $padSeqResult;
    $padSeqMerge2 = $padSeqStore [$padSeqMergeKey];

    $padSeqResult = [];

    $padSeqMerge1Val = reset ($padSeqMerge1);
    $padSeqMerge2Val = reset ($padSeqMerge2);

    while ( $padSeqMerge1Val !== FALSE or $padSeqMerge2Val !== FALSE) {

      if ( $padSeqMerge1Val !== FALSE and $padSeqMerge2Val === FALSE ) {
        if ($padSeqActionName == 'combine' or ! in_array($padSeqMerge1Val, $padSeqResult) )
          $padSeqResult [] = $padSeqMerge1Val;
        $padSeqMerge1Val = next ($padSeqMerge1);
      } elseif ( $padSeqMerge1Val === FALSE and $padSeqMerge2Val !== FALSE ) {
        if ($padSeqActionName == 'combine' or ! in_array($padSeqMerge2Val, $padSeqResult) )
          $padSeqResult [] = $padSeqMerge2Val;
        $padSeqMerge2Val = next ($padSeqMerge2);
      } elseif ( $padSeqMerge1Val < $padSeqMerge2Val ) {
        if ($padSeqActionName == 'combine' or ! in_array($padSeqMerge1Val, $padSeqResult) )
          $padSeqResult [] = $padSeqMerge1Val;
        $padSeqMerge1Val = next ($padSeqMerge1);
      } else {
        if ($padSeqActionName == 'combine' or ! in_array($padSeqMerge2Val, $padSeqResult) )
          $padSeqResult [] = $padSeqMerge2Val;
        $padSeqMerge2Val = next ($padSeqMerge2);
      }

    }

  }

  return $padSeqResult;

?>
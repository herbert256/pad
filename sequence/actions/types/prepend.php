<?php


  foreach ( $padSeqActionList as $padSeqAppendKey )
    foreach ($padSeqStore [$padSeqAppendKey] as $padSeqAppendValue)
      $padSeqResult [] = $padSeqAppendValue;

  return $padSeqResult;

?>
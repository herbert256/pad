<?php
 
  $padSeqAppend = padExplode ($padSeqActionValue, '|');

  foreach ( $padSeqAppend as $padSeqAppendKey )
    foreach ($padSeqStore [$padSeqAppendKey] as $padSeqAppendValue)
      $padSeqResult [] = $padSeqAppendValue;

  return $padSeqResult;

?>
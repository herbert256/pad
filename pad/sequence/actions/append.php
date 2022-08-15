<?php
 
  $padSeqAppend = padExplode ($padSeqActionValue, '|');

  foreach ( $padSeqAppend as $padSeqAppendKey )
    foreach ($padSequenceStore [$padSeqAppendKey] as $padSeqAppendValue)
      $padSeqResult [] = $padSeqAppendValue;

  return $padSeqResult;

?>
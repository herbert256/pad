<?php
    
  if ( $padSeqCheck == 'flag'   and ! $padSeqFlag   ) return;
  if ( $padSeqCheck == 'keep'   and ! $padSeqKeep   ) return;
  if ( $padSeqCheck == 'remove' and ! $padSeqRemove ) return;

  foreach ( $padSeqPlays as $padSeqPlay )
    if ( $padSeqPlay ['padSeqPlay'] == $padSeqCheck )
      return;
         
  if ( padSeqStore ( $padSeqBuild ) ) include 'sequence/inits/check/store.php';
  else                                include 'sequence/inits/check/sequence.php';

?>
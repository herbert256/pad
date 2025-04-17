<?php
    
  if ( $pqCheck == 'make'   and ! $pqMake   ) return;
  if ( $pqCheck == 'flag'   and ! $pqFlag   ) return;
  if ( $pqCheck == 'keep'   and ! $pqKeep   ) return;
  if ( $pqCheck == 'remove' and ! $pqRemove ) return;

  foreach ( $pqPlays as $pqPlay )
    if ( $pqPlay ['pqPlay'] == $pqCheck )
      return;
         
  if ( pqStore ( $pqBuild ) ) include 'sequence/inits/check/store.php';
  else                                include 'sequence/inits/check/sequence.php';

?>
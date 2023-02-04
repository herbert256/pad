<?php

  $padSeqOpr = [];

  foreach ( $padPrm [$pad] as $padSeqOprName => $padSeqOprValue )

    if ( $padSeqOprName <> $padSeqSeq )
    
      if ( $padSeqOprName == 'make' or $padSeqOprName == 'keep' or $padSeqOprName == 'remove' or 
           file_exists ( PAD . "sequence/types/$padSeqOprName/make.php" ) or 
           file_exists ( PAD . "sequence/types/$padSeqOprName/filter.php" ) 
         )

        $padSeqOpr [$padSeqOprName] = $padSeqOprValue;
    
?>
<?php

  $padSeqOpr = [];

  foreach ( $padPrm [$pad] as $padSeqOprName => $padSeqOprValue )

    if ( $padSeqOprName <> $padSeqSeq )
    
      if ( $padSeqOprName == 'make' or $padSeqOprName == 'keep' or $padSeqOprName == 'remove' or 
           padExists ( pad . "sequence/types/$padSeqOprName/make.php" ) or 
           padExists ( pad . "sequence/types/$padSeqOprName/filter.php" ) 
         )

        $padSeqOpr [$padSeqOprName] = $padSeqOprValue;
    
?>
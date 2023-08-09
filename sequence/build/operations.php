<?php

  $padSeqOpr = [];

  foreach ( $padPrm [$pad] as $padSeqOprName => $padSeqOprValue )

    if ( $padSeqOprName <> $padSeqSeq )
    
      if ( $padSeqOprName == 'make' or $padSeqOprName == 'keep' or $padSeqOprName == 'remove' or 
           padExists ( "$padSeqTypes/$padSeqOprName/make.php" ) or 
           padExists ( "$padSeqTypes/$padSeqOprName/filter.php" ) 
         )

        $padSeqOpr [$padSeqOprName] = $padSeqOprValue;
    
?>
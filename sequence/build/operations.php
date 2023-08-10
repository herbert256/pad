<?php


  $padSeqOpr = [];

  foreach ( $padPrm [$pad] as $padSeqOprName => $padSeqOprValue )

    if ( $padSeqOprName <> $padSeqSeq )

      if ( ! in_array ( $padSeqOprName , $padSeqMkr ) )
    
        if ( padExists ( "$padSeqTypes/$padSeqOprName/make.php" ) or 
             padExists ( "$padSeqTypes/$padSeqOprName/filter.php" ) 
           )

          $padSeqOpr [$padSeqOprName] = $padSeqOprValue;
    
?>
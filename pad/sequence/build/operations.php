<?php

  $padSeq_opr = [];

  foreach ( $padPrmsTag [$pad] as $padSeq_opr_name => $padSeq_opr_value )

    if ( $padSeq_opr_name <> $padSeq_seq )
    
      if ( $padSeq_opr_name == 'make' or $padSeq_opr_name == 'keep' or $padSeq_opr_name == 'remove' or 
           file_exists ( PAD . "sequence/types/$padSeq_opr_name/make.php" ) or 
           file_exists ( PAD . "sequence/types/$padSeq_opr_name/filter.php" ) 
         )

        $padSeq_opr [$padSeq_opr_name] = $padSeq_opr_value;
    
?>
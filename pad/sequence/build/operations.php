<?php

  $pSeq_opr = [];

  foreach ( $pPrmsTag[$p] as $pSeq_opr_name => $pSeq_opr_value )

    if ( $pSeq_opr_name <> $pSeq_seq )
    
      if ( $pSeq_opr_name == 'make' or $pSeq_opr_name == 'keep' or $pSeq_opr_name == 'remove' or 
           file_exists ( PAD . "sequence/types/$pSeq_opr_name/make.php" ) or 
           file_exists ( PAD . "sequence/types/$pSeq_opr_name/filter.php" ) 
         )

        $pSeq_opr [$pSeq_opr_name] = $pSeq_opr_value;
    
?>
<?php

  $pad_seq_opr = [];

  foreach ( $pad_parms_tag as $pad_seq_opr_name => $pad_seq_opr_value )

    if ( $pad_seq_opr_name <> $pad_seq_seq )
    
      if ( $pad_seq_opr_name == 'make' or 
           $pad_seq_opr_name == 'filter' or 
           pad_file_exists ( PAD . "sequence/types/$pad_seq_opr_name/make.php" ) or 
           pad_file_exists ( PAD . "sequence/types/$pad_seq_opr_name/filter.php" ) 
         )

        $pad_seq_opr [$pad_seq_opr_name] = $pad_seq_opr_value;
    
?>
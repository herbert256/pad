<?php
  
  foreach ( $pad_prms_tag as $pad_seq_action_name => $pad_seq_action_value )

    if ( $pad_seq_action_name <> $pad_seq_seq )

      if ( file_exists ( PAD . "sequence/actions/$pad_seq_action_name.php" ) ) {

        if ( $pad_seq_action_value === TRUE or ! ctype_digit($pad_seq_action_value) )
          if ( $pad_seq_count )
            $pad_seq_action_count = $pad_seq_count;
          else
            $pad_seq_action_count = 1;
        else
          $pad_seq_action_count = $pad_seq_action_value;    

        $pad_seq_result = include PAD . "sequence/actions/$pad_seq_action_name.php";

        pad_done (, $pad_seq_action_name, TRUE );

      }
  
?>
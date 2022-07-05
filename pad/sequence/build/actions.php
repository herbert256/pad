<?php
  
  foreach ( $pad_parms_tag as $pad_seq_action_name => $pad_seq_action_value )

    if ( $pad_seq_action_name <> $pad_seq_seq )

      if ( pad_file_exists ( PAD . "sequence/actions/$pad_seq_action_name.php" ) ) {

        include PAD . "sequence/actions/$pad_seq_action_name.php";

        pad_set_arr_var ( 'options_done', $pad_seq_action_name, TRUE );

      }
  
?>
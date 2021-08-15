<?php
  
  foreach ( $pad_parms_tag as $pad_seq_opt_name => $pad_seq_opt_value)
    if ( pad_file_exists ( PAD_HOME . "pad/sequence/options/$pad_seq_opt_name.php" ) ) {
      pad_set_arr_var ( 'options_done', $pad_seq_opt_name, TRUE );
      include PAD_HOME . "pad/sequence/options/$pad_seq_opt_name.php";
    }
  
?>
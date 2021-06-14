<?php

  $pad_seq_action_save = $pad_sequence;

  foreach ( $pad_seq_actions as $pad_seq_action ) 
    $pad_sequence = include PAD_HOME . "sequence/actions/$pad_seq_action.php"; 

  $pad_action_result = $pad_sequence;

  $pad_sequence = $pad_seq_action_save;

  return $pad_action_result;

?>
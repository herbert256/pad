<?php

  $pad_seq_merge_list = pad_explode ($pad_seq_action_value, '|');


  foreach ( $pad_seq_merge_list as $pad_seq_merge_key ) {

    $pad_seq_merge_1 = $pad_seq_result;
    $pad_seq_merge_2 = $pad_seq_store [$pad_seq_merge_key];

    $pad_seq_result = [];

    $pad_seq_merge_1_val = reset ($pad_seq_merge_1);
    $pad_seq_merge_2_val = reset ($pad_seq_merge_2);

    while ( $pad_seq_merge_1_val !== FALSE or $pad_seq_merge_2_val !== FALSE) {

      if ( $pad_seq_merge_1_val !== FALSE and $pad_seq_merge_2_val === FALSE ) {
        if ($pad_seq_action_name == 'combine' or ! in_array($pad_seq_merge_1_val, $pad_seq_result) )
          $pad_seq_result [] = $pad_seq_merge_1_val;
        $pad_seq_merge_1_val = next ($pad_seq_merge_1);
      } elseif ( $pad_seq_merge_1_val === FALSE and $pad_seq_merge_2_val !== FALSE ) {
        if ($pad_seq_action_name == 'combine' or ! in_array($pad_seq_merge_2_val, $pad_seq_result) )
          $pad_seq_result [] = $pad_seq_merge_2_val;
        $pad_seq_merge_2_val = next ($pad_seq_merge_2);
      } elseif ( $pad_seq_merge_1_val < $pad_seq_merge_2_val ) {
        if ($pad_seq_action_name == 'combine' or ! in_array($pad_seq_merge_1_val, $pad_seq_result) )
          $pad_seq_result [] = $pad_seq_merge_1_val;
        $pad_seq_merge_1_val = next ($pad_seq_merge_1);
      } else {
        if ($pad_seq_action_name == 'combine' or ! in_array($pad_seq_merge_2_val, $pad_seq_result) )
          $pad_seq_result [] = $pad_seq_merge_2_val;
        $pad_seq_merge_2_val = next ($pad_seq_merge_2);
      }

    }

  }

  return $pad_seq_result;

?>
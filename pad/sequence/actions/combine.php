<?php

  $padSeq_merge_list = padExplode ($padSeq_action_value, '|');


  foreach ( $padSeq_merge_list as $padSeq_merge_key ) {

    $padSeq_merge_1 = $padSeq_result;
    $padSeq_merge_2 = $padSequenceStore [$padSeq_merge_key];

    $padSeq_result = [];

    $padSeq_merge_1_val = reset ($padSeq_merge_1);
    $padSeq_merge_2_val = reset ($padSeq_merge_2);

    while ( $padSeq_merge_1_val !== FALSE or $padSeq_merge_2_val !== FALSE) {

      if ( $padSeq_merge_1_val !== FALSE and $padSeq_merge_2_val === FALSE ) {
        if ($padSeq_action_name == 'combine' or ! in_array($padSeq_merge_1_val, $padSeq_result) )
          $padSeq_result [] = $padSeq_merge_1_val;
        $padSeq_merge_1_val = next ($padSeq_merge_1);
      } elseif ( $padSeq_merge_1_val === FALSE and $padSeq_merge_2_val !== FALSE ) {
        if ($padSeq_action_name == 'combine' or ! in_array($padSeq_merge_2_val, $padSeq_result) )
          $padSeq_result [] = $padSeq_merge_2_val;
        $padSeq_merge_2_val = next ($padSeq_merge_2);
      } elseif ( $padSeq_merge_1_val < $padSeq_merge_2_val ) {
        if ($padSeq_action_name == 'combine' or ! in_array($padSeq_merge_1_val, $padSeq_result) )
          $padSeq_result [] = $padSeq_merge_1_val;
        $padSeq_merge_1_val = next ($padSeq_merge_1);
      } else {
        if ($padSeq_action_name == 'combine' or ! in_array($padSeq_merge_2_val, $padSeq_result) )
          $padSeq_result [] = $padSeq_merge_2_val;
        $padSeq_merge_2_val = next ($padSeq_merge_2);
      }

    }

  }

  return $padSeq_result;

?>
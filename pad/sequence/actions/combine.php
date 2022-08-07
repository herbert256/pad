<?php

  $pSeq_merge_list = pExplode ($pSeq_action_value, '|');


  foreach ( $pSeq_merge_list as $pSeq_merge_key ) {

    $pSeq_merge_1 = $pSeq_result;
    $pSeq_merge_2 = $pSequence_store [$pSeq_merge_key];

    $pSeq_result = [];

    $pSeq_merge_1_val = reset ($pSeq_merge_1);
    $pSeq_merge_2_val = reset ($pSeq_merge_2);

    while ( $pSeq_merge_1_val !== FALSE or $pSeq_merge_2_val !== FALSE) {

      if ( $pSeq_merge_1_val !== FALSE and $pSeq_merge_2_val === FALSE ) {
        if ($pSeq_action_name == 'combine' or ! in_array($pSeq_merge_1_val, $pSeq_result) )
          $pSeq_result [] = $pSeq_merge_1_val;
        $pSeq_merge_1_val = next ($pSeq_merge_1);
      } elseif ( $pSeq_merge_1_val === FALSE and $pSeq_merge_2_val !== FALSE ) {
        if ($pSeq_action_name == 'combine' or ! in_array($pSeq_merge_2_val, $pSeq_result) )
          $pSeq_result [] = $pSeq_merge_2_val;
        $pSeq_merge_2_val = next ($pSeq_merge_2);
      } elseif ( $pSeq_merge_1_val < $pSeq_merge_2_val ) {
        if ($pSeq_action_name == 'combine' or ! in_array($pSeq_merge_1_val, $pSeq_result) )
          $pSeq_result [] = $pSeq_merge_1_val;
        $pSeq_merge_1_val = next ($pSeq_merge_1);
      } else {
        if ($pSeq_action_name == 'combine' or ! in_array($pSeq_merge_2_val, $pSeq_result) )
          $pSeq_result [] = $pSeq_merge_2_val;
        $pSeq_merge_2_val = next ($pSeq_merge_2);
      }

    }

  }

  return $pSeq_result;

?>
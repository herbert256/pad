<?php

  $pad_app_ob = '';

  ob_start();

  $pad_tag_result = include PAD_HOME . "types/$pad_tag_type.php";

  $pad_walks [$pad_lvl] = $pad_walk; 

  $pad_tag_ob = ob_get_clean() . $pad_app_ob;

  $pad_true_false [$pad_lvl] = pad_true_false ($pad_tag_result);

  $pad_tag_result_type = pad_analyze_var ($pad_tag_result);
  
  if ($pad_trace) {
    $pad_trace2 = pad_var_data ($pad_tag_result) . $pad_tag_ob; 
    pad_trace ("tag/$pad_walk", "tag=$pad_tag result_type=$pad_tag_result_type|result_data=" . $pad_trace2, TRUE);
  }

?>
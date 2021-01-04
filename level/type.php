<?php

  pad_trace ("type", "$pad_tag_type.php ($pad_tag)", TRUE);

  $pad_app_ob = '';
  ob_start();
  $pad_tag_result = include PAD_HOME . "types/$pad_tag_type.php";
  $pad_tag_ob = ob_get_clean() . $pad_app_ob;

  $pad_walks [$pad_lvl]      = $pad_walk; 
  $pad_true_false [$pad_lvl] = pad_true_false ($pad_tag_result);
  
  pad_trace ("tag/result", pad_analyze_var ($pad_tag_result), TRUE);

  return $pad_tag_result;

?>
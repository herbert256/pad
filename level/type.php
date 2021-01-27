<?php

  pad_trace ("tag/type", "type=$pad_tag_type tag=$pad_tag", TRUE);

  $pad_tag_content = '';

  ob_start();

  $pad_tag_result = include PAD_HOME . "types/$pad_tag_type.php";

  $pad_tag_content .= ob_get_clean();

  $pad_walks [$pad_lvl]      = $pad_walk; 
  $pad_true_false [$pad_lvl] = pad_true_false ($pad_tag_result);
  
  pad_trace ("tag/result", pad_analyze_var ($pad_tag_result), TRUE);

  return $pad_tag_result;

?>
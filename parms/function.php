<?php

  $pad_tag_save      = $pad_tag;
  $pad_tag_type_save = $pad_tag_type;

  $pad_function_result = include PAD_HOME . 'tags/function.php';

  $pad_tag      = $pad_tag_save;
  $pad_tag_type = $pad_tag_type_save;

  return $pad_function_result;

?>
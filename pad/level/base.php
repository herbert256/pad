<?php

  if     ( $pad_null  ) $pad_base [$pad] = '';
  elseif ( $pad_else  ) $pad_base [$pad] = $pad_false;    
  elseif ( $pad_array ) $pad_base [$pad] = $pad_content;    
  elseif ( $pad_text  ) $pad_base [$pad] = $pad_tag_result;
  else                  $pad_base [$pad] = $pad_content;

  $pad_content = $pad_base [$pad];
    
?>
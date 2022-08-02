<?php

  if ( $pad_null ) {

    $pad_base [$pad_lvl] = '';
   
  } elseif ( $pad_else ) {
 
    $pad_base [$pad_lvl] = $pad_false;
    
  } else {

    if ( is_array($pad_tag_result) or $pad_tag_result === TRUE  )     
      $pad_base [$pad_lvl] = $pad_content;
    elseif ( strpos($pad_tag_result , '@content@') === FALSE)
      $pad_base [$pad_lvl] = $pad_tag_result . $pad_content;
    else
      $pad_base [$pad_lvl] = str_replace('@content@', $pad_content, $pad_tag_result);

    $pad_base [$pad_lvl] .= $pad_tag_content;

  }

  $pad_content = $pad_base [$pad_lvl];
  
?>
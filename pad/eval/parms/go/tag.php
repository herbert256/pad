<?php
 
  $pad_eval_tag_options = '';

  if ( $value )
    $pad_eval_tag_options = $value;
  else
    $pad_eval_tag_options = '';
 
  foreach ($parm as $pad_k => $pad_v)
    if ( $pad_eval_tag_options)
      $pad_eval_tag_options .= ", '$pad_v'";
    else
      $pad_eval_tag_options .= $pad_v;

  return pad_tag_as_function ( "$pad_eval_tag_type:$name", $pad_eval_tag_options);
  
?>
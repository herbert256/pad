<?php
 
  $padEval_tag_options = '';

  if ( $value )
    $padEval_tag_options = $value;
  else
    $padEval_tag_options = '';
 
  foreach ($padarm as $padK => $padV)
    if ( $padEval_tag_options)
      $padEval_tag_options .= ", '$padV'";
    else
      $padEval_tag_options .= $padV;

  return pTag_as_function ( "$padEval_tag_type:$name", $padEval_tag_options);
  
?>
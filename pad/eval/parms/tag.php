<?php

  if     ( file_exists ( APP . "tags/$name.php"  ) ) $padEval_tag_type= 'app';
  elseif ( file_exists ( PAD . "tags/$name.php"  ) ) $padEval_tag_type= 'app';
  else                                               return '';
  
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
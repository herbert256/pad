<?php
 
  $padEvalTagOptions = '';

  if ( $value )
    $padEvalTagOptions = $value;
  else
    $padEvalTagOptions = '';
 
  foreach ($parm as $padK => $padV)
    if ( $padEvalTagOptions)
      $padEvalTagOptions .= ", '$padV'";
    else
      $padEvalTagOptions .= $padV;

  return pTag_as_function ( "$padEvalTagType:$name", $padEvalTagOptions);
  
?>
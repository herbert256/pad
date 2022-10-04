<?php

  if     ( file_exists ( APP . "tags/$name.php"  ) ) $padEvalTagType= 'app';
  elseif ( file_exists ( PAD . "tags/$name.php"  ) ) $padEvalTagType= 'pad';
  else                                               return '';
  
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
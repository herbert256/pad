<?php
  
  $padEvalTagOptions = $value;
 
  foreach ($parm as $padK => $padV)
    if ( $padEvalTagOptions)
      $padEvalTagOptions .= ", '$padV'";
    else
      $padEvalTagOptions .= $padV;

  return padTagAsFunction ( $name, $padEvalTagOptions );

?>
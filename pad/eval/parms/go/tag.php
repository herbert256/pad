<?php
 
  $pEval_tag_options = '';

  if ( $value )
    $pEval_tag_options = $value;
  else
    $pEval_tag_options = '';
 
  foreach ($parm as $pK => $pV)
    if ( $pEval_tag_options)
      $pEval_tag_options .= ", '$pV'";
    else
      $pEval_tag_options .= $pV;

  return pTag_as_function ( "$pEval_tag_type:$name", $pEval_tag_options);
  
?>
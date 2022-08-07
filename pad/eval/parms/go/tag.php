<?php
 
  $pEval_tag_options = '';

  if ( $value )
    $pEval_tag_options = $value;
  else
    $pEval_tag_options = '';
 
  foreach ($parm as $pK => $pad_v)
    if ( $pEval_tag_options)
      $pEval_tag_options .= ", '$pad_v'";
    else
      $pEval_tag_options .= $pad_v;

  return pTag_as_function ( "$pEval_tag_type:$name", $pEval_tag_options);
  
?>
<?php

  if     ( file_exists ( APP . "tags/$name.php"  ) ) $pEval_tag_type= 'app';
  elseif ( file_exists ( PAD . "tags/$name.php"  ) ) $pEval_tag_type= 'app';
  else                                               return '';
  
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
<?php
 
  $pad_between = $name;

  if ($value)
    $pad_between .= " '$value'";
 
  foreach ($parm as $pad_k => $pad_v)
    $pad_between .= ", '$pad_v' ";

  return pad_tag_as_function ( $pad_tag_type, $pad_between );
  
?>
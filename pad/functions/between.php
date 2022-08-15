<?php
  
  if ( $count <> 2 )
    padError ("Function 'between' must have exactly 2 parameters");

  if ( ($value > $parm[0]) and ($value < $parm[1]) )
    return '1';
  else
    return '';
  
?>
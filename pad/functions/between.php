<?php
  
  if ( $count <> 2 )
    pError ("Function 'between' must have exactly 2 parameters");

  if ( ($value > $padarm[0]) and ($value < $padarm[1]) )
    return '1';
  else
    return '';
  
?>
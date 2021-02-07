<?php
  
  if ( $count <> 2 )
    pad_eval_error ("Function 'between' must have exactly 2 parameters");

  pad_trace ("xxx", $value . '-' . $parm[0] . '-' . $parm[1] . '-');

  if ( ($value > $parm[0]) and ($value < $parm[1]) )
    return '1';
  else
    return '';
  
?>
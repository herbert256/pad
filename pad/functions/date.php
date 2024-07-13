<?php

  if ( $count == 2 ) {
    
    $date = date_create_from_format($parm[0], $value);
    return $date->format($parm[1]);

  } elseif ( $count == 0 )
  
    $format = $GLOBALS ['padFmt_'.$name];
    
  elseif ( $count == 1 )

    $format = $parm[0];

  return date($format, $value);

?>
<?php

  if ( $count == 2 ) {
    
    $date = date_create_from_format($padarm[0], $value);
    return $date->format($padarm[1]);

  } elseif ( $count == 0 )
  
    $format = $GLOBALS ['padFmt_'.$name];
    
  elseif ( $count == 1 )

    $format = $padarm[0];

  return date($format, $value);

?>
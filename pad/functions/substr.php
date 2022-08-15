<?php

  if ( $padarmCnt == 1 )
  
    return substr($value, $padarm_1_int);
    
  elseif ( $padarmCnt == 2 )

    return substr($value, $padarm_1_int, $padarm_2_int);

  else

    pError ("There must be one or two parameters for $name");

?>

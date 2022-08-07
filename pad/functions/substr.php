<?php

  if ( $parmCnt == 1 )
  
    return substr($value, $parm_1_int);
    
  elseif ( $parmCnt == 2 )

    return substr($value, $parm_1_int, $parm_2_int);

  else

    pError ("There must be one or two parameters for $name");

?>

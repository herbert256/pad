<?php

  if ( $parm_count == 1 )
  
    return substr($value, $parm_1_int);
    
  elseif ( $parm_count == 2 )

    return substr($value, $parm_1_int, $parm_2_int);

  else

    pError ("There must be one or two parameters for $name");

?>

<?php

  if ( $parmCnt == 1 )
  
    return substr($value, $parm_1_int);
    
  else

    return substr($value, $parm_1_int, $parm_2_int);

?>
<?php

return padLevel ( $padBetweenOrg );

  $padReturn = padEval ( $padBetweenOrg );

  if ( is_array ( $padReturn ) )
    return padLevel ( $padBetweenOrg );
  else
    return padLevel ( $padReturn );

?>

<?php

  $padReturn = padEval ( $padBetweenOrg );

  if ( is_array ( $padReturn ) )  
    return padLevelNo ( $padBetweenOrg );
  else
    return padLevelNo ( $padReturn );


  padLevelNo ( $padReturn );

?>
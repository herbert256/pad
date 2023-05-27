<?php
  
  global $padData, $padKey;

  #return ( $padKey [$padIdx] == array_key_first  ( $padData [$padIdx] ) );

  return ( (include "tag/current.php") == 1 );

?>
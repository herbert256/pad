<?php
  
  global $padData, $padKey;

  #return ( $padKey [$padIdx] == array_key_first  ( $padData [$padIdx] ) );

  return ( (include PAD . "tag/current.php") == 1 );

?>
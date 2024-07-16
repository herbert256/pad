<?php

  global $padData, $padKey;
  
  return ( $padKey [$padIdx] == array_key_last ( $padData [$padIdx] ) );

?>
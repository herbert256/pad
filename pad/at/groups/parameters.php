<?php

  global $padOpt;

  $padOptAt = $padOpt [$padIdx];
 
  unset ( $padOptAt [0] );
 
  return padAtSearch ( $padOptAt , $names );
  
?>
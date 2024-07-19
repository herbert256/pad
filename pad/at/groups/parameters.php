<?php

  global $padOpt;

  $padOptAt = $padOpt [$padIdx];
 
  unset ( $padOptAt [0] );
 
  return padFindNames ( $padOptAt , $names );
  
?>
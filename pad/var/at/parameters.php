<?php

  $padOptAt = $padOpt [$i];
  unset ( $padOptAt [0] );

  if ( ! count ($names) )
    return padDataForcePad ($padOptAt); 
  
  return padAtSearch ( $padOptAt, $names );

?>
<?php

  global $padCurrent;
  
  if ( ! isset ($padCurrent [$padIdx]) )
    return INF;
  
  return padAtSearch ( $padCurrent [$padIdx], $names );

?>
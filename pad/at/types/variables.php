<?php

  if ( ! count ($names) )
    return padDataForcePad ( $padSetLvl [$padIdx] ); 

  return padAtSearch ( $padSetLvl [$padIdx], $names );

?>
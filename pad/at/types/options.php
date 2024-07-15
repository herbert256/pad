<?php

  if ( ! count ($names) )
    return padDataForcePad ( $padPrm [$padIdx] ); 

  return padAtSearch ( $padPrm [$padIdx], $names );

?>
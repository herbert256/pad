<?php

  global $padPrmsTag;

  if ( isset ( $padPrmsTag [$padIdx] [$padarm] ) )
    return $padPrmsTag [$padIdx] [$padarm];
  else
    return NULL;

?>
<?php

  global $padPrmsTag;

  if ( isset ( $padPrmsTag [$padIdx] [$parm] ) )
    return $padPrmsTag [$padIdx] [$parm];
  else
    return NULL;

?>
<?php

  if ( isset ( $padDataStore [ $padParm ] ) )
    if ( count ( $padDataStore [ $padParm ] ) == 0 )
      return FALSE;
    else
      return TRUE;

  if ( count ( $GLOBALS [ $padParm ] ) == 0 )
    return FALSE;
  else
    return TRUE;

?>
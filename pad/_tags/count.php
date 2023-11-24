<?php

  if ( isset ( $padDataStore [ $padOpt [$pad] [1] ] ) )
    if ( count ( $padDataStore [ $padOpt [$pad] [1] ] ) == 0 )
      return FALSE;
    else
      return TRUE;

  if ( count ( $GLOBALS [ $padOpt [$pad] [1] ] ) == 0 )
    return FALSE;
  else
    return TRUE;

?>
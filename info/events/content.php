<?php

  if ( ! $GLOBALS ['padInfoXapp'] ) 
    return;

  if ( $padDouble [$pad] == 'mrg-new' or $padDouble [$pad] == 'mrg-base' )
    if ( $GLOBALS ['padInfoXapp'] ) padInfoXapp ( 'constructs', 'content' );

  if ( $padDouble [$pad] == 'mrg-new' or $padDouble [$pad] == 'mrg-base' )
   if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'constructs', 'content' );

?>
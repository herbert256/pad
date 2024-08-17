<?php

  if ( ! $GLOBALS ['padInfoXref'] )
  	return;

 padInfoXref ( 'walk', 'step-' . $padWalk [$pad], $padTag  [$pad] );
 padInfoXref ( 'walk', 'tag-'  . $padTag [$pad],  $padWalk [$pad] );

?>
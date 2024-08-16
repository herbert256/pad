<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
  	return;

 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'walk', 'step-' . $padWalk [$pad], $padTag  [$pad] );
 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'walk', 'tag-'  . $padTag [$pad],  $padWalk [$pad] );

?>
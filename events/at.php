<?php

  return;

  if ( $field    ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'at', 'fields',     $field    );
  if ( $kind     ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'at', 'kinds',      $kind     );
  if ( $name     ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'at', 'names',      $name     );
  if ( $property ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'at', 'properties', $property );

  if ( $kind     ) if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) padInfoXapp ( 'at', 'kinds',      $kind     );
  if ( $property ) if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) padInfoXapp ( 'at', 'properties', $property );

  if ( $field    ) if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) padInfoXapp ( 'at-2', 'fields',     $field    );
  if ( $kind     ) if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) padInfoXapp ( 'at-2', 'kinds',      $kind     );
  if ( $name     ) if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) padInfoXapp ( 'at-2', 'names',      $name     );
  if ( $property ) if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) padInfoXapp ( 'at-2', 'properties', $property );

?>
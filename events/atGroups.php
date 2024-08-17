<?php

  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) {
    padInfoXapp  ( 'at', 'groups', $group );
    padInfoXapp  ( 'properties',   $group );
  }

?>
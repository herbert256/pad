<?php

  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) {

    padInfoXapp ( 'sequences', 'types', $padSeqSeq );

    if ( $padSeqFor !== FALSE )
      padInfoXapp ( 'sequences', 'builds', 'for' );
    else
      padInfoXapp ( 'sequences', 'builds', $padSeqBuild );

  }

?>
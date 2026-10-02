<?php

  function pqParm ( $type ) {

    $parm = $a = $b = $e = '';

    $build = pqBuild ( $type );

    if ( $build != 'order' and $build != 'fixed' ) {
      $a = padCode ( "{sequence $type=3, rows=15}{\$sequence}{/sequence}" );
      $b = padCode ( "{sequence $type=5, rows=15}{\$sequence}{/sequence}" );
    }

    // The type's own files, under the engine's sequence types - a bare types/ path resolved
    // against this application's directory, so the scan never found one and only the
    // comparison of the two runs below caught a type that reads its parameter.

    foreach ( [ 'loop', 'make', 'function', 'bool', 'fixed', 'build' ] as $check )
      if ( file_exists ( PT . "$type/$check.php" ) )
        if ( str_contains ( padFileGet ( PT . "$type/$check.php" ), "pqParm" ) )
          $e = TRUE;

    if ( $e or $a != $b )
      $parm = '=4';

    return $parm;

  }

?>
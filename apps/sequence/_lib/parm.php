<?php

  function pqParm ( $type ) {

    $parm = $a = $b = $e = '';

    $build = pqBuild ( $type );

    if ( $build <> 'order' and $build <> 'fixed' ) {
      $a = padCode ( "{sequence $type=3, rows=15}{\$sequence}{/sequence}" );
      $b = padCode ( "{sequence $type=5, rows=15}{\$sequence}{/sequence}" );
    }

    foreach ( [ 'loop', 'make', 'function', 'bool', 'fixed', 'build' ] as $check )
      if ( file_exists ( "types/$type/$check.php") )
        if ( str_contains ( padFileGet ( "types/$type/$check.php"), "pqParm" ) )
          $e = TRUE;

    if ( $e or $a <> $b )
      $parm = '=4';

    return $parm;

  }

?>
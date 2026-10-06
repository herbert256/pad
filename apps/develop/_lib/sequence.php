<?php

  // Whether a sequence type takes a parameter, as the sequence build asks it (sequence/
  // parm.php): '=4' - the parameter the generated pages show it with, add=4 - when it does,
  // '' when it does not. It takes one when its code reads $pqParm, or when it answers 3 and
  // 5 differently. This stood in the sequence application's _lib/ as pqParm until the
  // reorganisation of December 2025 (565cff2d7) left it out, and the build has called a
  // function no part of PAD defines since - "Call to undefined function pqParm()".

  function developSequenceParm ( $type ) {

    foreach ( [ 'loop', 'make', 'function', 'bool', 'fixed', 'build' ] as $check )
      if ( str_contains ( (string) @file_get_contents ( PT . "$type/$check.php" ), 'pqParm' ) )
        return '=4';

    $build = pqBuild ( $type );

    if ( $build == 'order' or $build == 'fixed' )
      return '';

    $three = padCode ( "{sequence $type=3, rows=15}{\$sequence}{/sequence}" );
    $five  = padCode ( "{sequence $type=5, rows=15}{\$sequence}{/sequence}" );

    return ( $three !== $five ) ? '=4' : '';

  }

?>

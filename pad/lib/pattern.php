<?php

  // The patterns: the files of subs/images/patterns/types/ (padKinds, lib/subsystem.php) - a kind is
  // added by adding its file there. The rest is the subsystem in subs/images/patterns/, its library
  // subs/images/patterns/_lib/ loaded when a page draws one.
  //
  //   $hero = padPattern ( 'topography', [ 'seed' => 7, 'width' => 960, 'height' => 320 ] );
  //   $css  = padPattern ( 'dots', [ 'as' => 'url', 'colors' => [ '#fff', '#c9d6e3' ] ] );
  //
  // padPattern       the SVG of a pattern - or with 'as' => 'url' its CSS url() - the same
  //                  bytes as {pattern:dots} writes for the same options; a wrong argument is
  //                  reported with padError, an unknown kind answers ''
  // padPatternKinds  the kinds, sorted

  function padPatternKinds () {

    return padKinds ( 'images/patterns' );

  }

  function padPattern ( $kind, $options = [] ) {

    include_once PAD . 'subs/images/patterns/_lib/pattern.php';

    $kind = strtolower ( trim ( (string) $kind ) );

    if ( ! padKind ( 'images/patterns', $kind ) ) {
      padError ( "padPattern: there is no pattern named '" . padMakeSafe ( $kind, 20 ) . "' - " . implode ( ', ', padPatternKinds () ) );
      return '';
    }

    if ( ! is_array ( $options ) ) {
      padError ( "padPattern: the options are an array - [ 'seed' => 7, 'size' => 40 ]" );
      $options = [];
    }

    $known = padPatternOptions ( $kind );

    foreach ( $options as $name => $value )
      if ( ! in_array ( $name, $known, TRUE ) ) {
        padError ( "padPattern: the pattern $kind has no option '" . padMakeSafe ( (string) $name, 20 ) . "' - " . implode ( ', ', $known ) );
        unset ( $options [$name] );
      }

    return padPatternMake ( $kind, $options, fn ( $message ) => padError ( "padPattern: $message" ) );

  }

?>

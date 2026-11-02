<?php

  // Case-sensitive symbols; conversion to the canonical unit is value * scale + offset.
  function padUnitsDefinitions ( $family = NULL ) {
    static $definitions;
    if ( $definitions === NULL ) {
      $definitions = [];
      foreach ( padKinds ( 'units' ) as $name )
        $definitions [$name] = include PAD . "subs/units/types/$name.php";
    }
    if ( $family === NULL )
      return $definitions;
    if ( ! is_string ( $family ) or ! isset ( $definitions [$family] ) ) {
      padError ( 'padUnits: unknown quantity family' );
      return NULL;
    }
    return $definitions [$family];
  }

  function padUnitsConvert ( $value, $from, $to ) {
    if ( ! is_numeric ( $value ) or ! is_finite ( (float) $value ) or ! is_string ( $from ) or ! is_string ( $to ) ) {
      padError ( 'padConvert: expected a finite number and two unit symbols' );
      return NULL;
    }
    $found = [ [], [] ];
    foreach ( padUnitsDefinitions () as $family => $definition )
      foreach ( $definition ['units'] as $symbol => $unit )
        foreach ( [ $from, $to ] as $i => $wanted )
          if ( $wanted === $symbol or in_array ( $wanted, $unit ['aliases'], TRUE ) )
            $found [$i] [] = [ $family, $unit ];
    if ( count ( $found [0] ) !== 1 or count ( $found [1] ) !== 1 or $found [0][0][0] !== $found [1][0][0] ) {
      padError ( 'padConvert: unknown, ambiguous or dimensionally incompatible units' );
      return NULL;
    }
    $a = $found [0][0][1];
    $b = $found [1][0][1];
    $canonical = (float) $value * $a ['scale'] + $a ['offset'];
    $result = ( $canonical - $b ['offset'] ) / $b ['scale'];
    if ( ! is_finite ( $result ) or ( $found [0][0][0] === 'temperature' and $canonical < -1e-10 ) ) {
      padError ( 'padConvert: conversion exceeds the physical or numeric range' );
      return NULL;
    }
    return $result;
  }

?>

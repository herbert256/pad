<?php

  // Directory-driven text and structured-data comparisons.
  include_once PAD . 'subs/diffs/_lib/diff.php';
  include_once PAD . 'subs/diffs/_lib/structured.php';
  if ( ! padKind ( 'diffs', $padDiffKind ) ) {
    if ( $padCheckSyntax ) padError ( "there is no diff type named '" . padMakeSafe ( $padDiffKind, 20 ) . "' - " . implode ( ', ', padKinds ( 'diffs' ) ) );
    return FALSE;
  }
  if ( in_array ( $padDiffKind, [ 'json', 'rows' ], TRUE ) ) {
    $padDiffArguments = [];
    $padDiffOptions = [];
    foreach ( padAttrsItems () as [ $padDiffName, $padDiffExpression ] ) {
      if ( $padDiffName === '' and $padDiffExpression === 'same' ) {
        $padDiffName = 'same';
        $padDiffExpression = 'TRUE';
      }
      if ( $padDiffName === '' ) $padDiffArguments [] = padDiffArgument ( $padDiffExpression );
      elseif ( ! in_array ( $padDiffName, [ 'from', 'to', 'key', 'same' ], TRUE ) ) {
        if ( $padCheckSyntax ) padError ( 'a structured diff accepts from, to, key and same options' );
      } elseif ( array_key_exists ( $padDiffName, $padDiffOptions ) ) {
        if ( $padCheckSyntax ) padError ( 'a structured diff option is written twice' );
      } else $padDiffOptions [$padDiffName] = padEval ( $padDiffExpression );
    }
    if ( count ( $padDiffArguments ) !== 2 ) {
      if ( $padCheckSyntax ) padError ( 'a structured diff requires exactly two values' );
      return FALSE;
    }
    $padOpt [$pad] [1] = $padParm = $padDiffArguments [0];
    $padOpt [$pad] [2] = $padDiffArguments [1];
    foreach ( $padDiffOptions as $padDiffName => $padDiffValue ) $padPrm [$pad] [$padDiffName] = $padDiffValue;
  }
  if ( ! array_key_exists ( 2, $padOpt [$pad] ) ) {
    if ( $padCheckSyntax ) padError ( 'the diff has no second text or value - write {diff:words $old, $new/}' );
    return FALSE;
  }
  $padDiffOld = $padParm;
  $padDiffNew = $padOpt [$pad] [2];
  $padDiffFrom = padTagParm ( 'from', 'Before' );
  $padDiffTo = padTagParm ( 'to', 'After' );
  if ( ! is_scalar ( $padDiffFrom ) or ! is_scalar ( $padDiffTo ) ) {
    if ( $padCheckSyntax ) padError ( 'the from and to labels of a diff must be text' );
    return FALSE;
  }
  $padDiffFrom = padUnprotect ( (string) $padDiffFrom );
  $padDiffTo = padUnprotect ( (string) $padDiffTo );
  if ( in_array ( $padDiffKind, [ 'words', 'lines', 'side' ], TRUE ) ) {
    if ( ( ! is_scalar ( $padDiffOld ) and $padDiffOld !== NULL ) or ( ! is_scalar ( $padDiffNew ) and $padDiffNew !== NULL ) ) {
      if ( $padCheckSyntax ) padError ( 'a text diff requires two scalar texts; use diff:json or diff:rows for structured values' );
      return FALSE;
    }
    $padDiffOld = padUnprotect ( (string) $padDiffOld );
    $padDiffNew = padUnprotect ( (string) $padDiffNew );
    // Preserve the old unlabelled text layouts unless labels were supplied.
    if ( ! isset ( $padPrm [$pad] ['from'] ) ) $padDiffFrom = '';
    if ( ! isset ( $padPrm [$pad] ['to'] ) ) $padDiffTo = '';
  }
  if ( $padInfoXref ?? FALSE ) padInfoXref ( 'diff', $padDiffKind );
  $padDiffType = include PAD . "subs/diffs/types/$padDiffKind.php";
  return $padDiffType ['render'] ( $padDiffOld, $padDiffNew, [
    'from' => $padDiffFrom, 'to' => $padDiffTo,
    'same' => (bool) padTagParm ( 'same', FALSE ), 'key' => padTagParm ( 'key' ),
    'lines' => (bool) padTagParm ( 'lines', FALSE ), 'side' => (bool) padTagParm ( 'side', FALSE ),
  ] );

?>

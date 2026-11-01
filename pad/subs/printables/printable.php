<?php

  // The body of a printable, whichever way it is written: {printable 'labels'} ... {/printable},
  // or the printable: type - {printable:labels} ... {/printable:labels}. The caller sets
  // $padPrintableKind; a printable is a file of subs/printables/types/, and the files there
  // are all the kinds there are (padPrintableKinds, lib/printable.php).
  //
  // The content between the tags is the template of one item. On its first walk the pair
  // puts a marker in front of it and asks for the end walk: the level then renders the
  // content once per row of its data - data=, and the handling options, sort= and where= and
  // the rest, as for any tag - and on the end walk the rendering is cut at the markers into
  // the items, which the kind lays out on its sheets. A level left without rows renders its
  // @else@ half, which has no marker and stays as it is. Written as a single tag the
  // printable has one item without content: a sheet of blank labels, an envelope of its
  // sender alone.

  include_once PAD . 'subs/printables/_lib/printable.php';

  if ( ! padPrintableKind ( $padPrintableKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no printable named '" . padMakeSafe ( $padPrintableKind, 20 ) . "' - " . implode ( ', ', padPrintableKinds () ) );
    return $padPair [$pad] ? TRUE : '';
  }

  if ( $padWalk [$pad] == 'start' ) {

    // The kind shown goes on the xref record, which the reference application shows: the
    // pages that show each kind.

    if ( $padInfoXref ?? FALSE )
      padInfoXref ( 'printable', $padPrintableKind );

    if ( $padPair [$pad] ) {
      $padContent     = PAD_PRINTABLE_ITEM . $padContent;
      $padWalk [$pad] = 'end';
      return TRUE;
    }

    // The markup is a value: a level loops over the data a data= option named, and would
    // write the sheets once per row - so the level gets its one default occurrence back.

    $padPrintableHtml = padPrintableTag ( $padPrintableKind, [ '' ] );

    $padData [$pad] = padDefaultData ();

    return $padPrintableHtml;

  }

  $padPrintableItems = padPrintableSplit ( $padContent );

  if ( $padPrintableItems !== NULL )
    $padContent = padPrintableTag ( $padPrintableKind, $padPrintableItems );

  return TRUE;

?>

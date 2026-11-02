<?php

  // The shared body of jsonld:kind tags, such as {jsonld:article}. The caller sets
  // $padJsonLdKind; registered kinds are files in subs/jsonld/types/.
  //
  // The fields come from the row or rows data= names - a {pad:data} store, the page's array, a
  // field holding a list, a _data file, as a widget reads them - and from options of their
  // names, which win over a row's field of the same name: {jsonld:article data='post',
  // url=$url/}. Each option is read with padTagParm, so one the kind does not take is
  // left unread and named by the strict check. id= is the object's @id. The handling options
  // - first=, where=, sort= - work on the rows, as on any data tag's.
  //
  // The script is a value, so the tag is single: content between its tags is an error. A
  // block with nothing to write - data= found no rows, the lenient walk left out an object
  // without its required fields - answers nothing, and its @else@ shows.

  include_once PAD . 'subs/jsonld/_lib/jsonld.php';

  if ( $padJsonLdKind === '' ) {
    if ( $padCheckSyntax )
      padError ( "the {jsonld:article} has no kind - {jsonld:article headline='...'/}, one of " . implode ( ', ', padJsonLdKinds () ) );
    return '';
  }

  if ( ! padKind ( 'jsonld', $padJsonLdKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no JSON-LD kind named '" . padMakeSafe ( $padJsonLdKind, 20 ) . "' - " . implode ( ', ', padJsonLdKinds () ) );
    return '';
  }

  // The kind written goes on the xref record, which the reference application shows: the
  // pages that write each kind.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'jsonld', $padJsonLdKind );

  if ( trim ( $padContent ) !== '' and $padCheckSyntax )
    padError ( "the JSON-LD $padJsonLdKind takes no content - it is a single tag, {jsonld:$padJsonLdKind .../}" );

  $padContent = '';

  // The rows data= names, NULL without it. The level got them as its data, and would write
  // the script once per row: it gets its one default occurrence back. The handling options
  // run on the rows here, so the level does not run them again. Handling that left no row
  // gives the level the one row of its @else@ - or, beside else=, notOk= or error=, the one
  // row of the content standing in (options/_go/reset.php): no rows either way.

  include_once PAD . 'subs/widgets/_lib/widget.php';

  $padJsonLdRows = padWidgetRows ( "jsonld:$padJsonLdKind" );

  if ( $padJsonLdRows !== NULL ) {

    // Plain values come as a list. A {pad:data} block of one record is made rows of one field
    // too, keyed by the record's names: those stay rows, never a value each - a password of
    // the record would have been written as a name.

    $padJsonLdNames = ( is_array ( $padJsonLdRows ) and ! array_filter ( array_keys ( $padJsonLdRows ), 'is_string' ) )
                    ? [ $padPrm [$pad] ['data'] ?? '', padDataName ( '' ) ] : [];

    $padJsonLdRows = padJsonLdRows ( $padJsonLdRows );

    if ( $padJsonLdRows and padJsonLdHandles () ) {
      $padData [$pad]     = padData ( $padJsonLdRows );
      include PAD . 'handling/handling.php';
      $padJsonLdRows      = ( $padElse [$pad] or ( $padHandStoodIn [$pad] ?? 0 ) === $padLevelId [$pad] ) ? [] : array_values ( $padData [$pad] );
      $padHandDone [$pad] = TRUE;
    }

    $padJsonLdRows = padJsonLdPlain ( padJsonLdSpec ( $padJsonLdKind ), $padJsonLdRows, $padJsonLdNames );

  }

  $padData [$pad] = padDefaultData ();

  // A data= that found no rows is a miss: its options are read, as a miss uses none of them.

  if ( $padJsonLdRows === [] ) {
    padJsonLdOptions ( padJsonLdSpec ( $padJsonLdKind ) );
    padTagParm ( 'id' );
    return FALSE;
  }

  $padJsonLdObject = padJsonLdMake ( $padJsonLdKind, $padJsonLdRows, padJsonLdOptions ( padJsonLdSpec ( $padJsonLdKind ) ),
                                     padJsonLdOption ( 'id', padTagParm ( 'id', NULL ) ),
                                     fn ( $message ) => $GLOBALS ['padCheckSyntax'] ? padError ( $message ) : NULL );

  return ( $padJsonLdObject === FALSE ) ? FALSE : padJsonLdScript ( $padJsonLdObject );

?>

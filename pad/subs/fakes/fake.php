<?php

  // The shared body of fake:kind tags, such as {fake:email}. The caller sets
  // $padFakeSpec; registered kinds are files in subs/fakes/types/.
  // {fake:rows 'customer:name, total:price', rows=3} selects several field kinds
  // from its first parameter; other kinds generate their own value or column.
  //
  // One value of one kind is the tag's answer - a FALSE of boolean is a miss, its @else@
  // shows. rows= makes so many rows, a pair one row unless rows= says more, and so do
  // several kinds: the level's data, iterated by the pair's content or kept by toData=, the
  // fields named after their kinds or as 'field:kind' says.
  //
  // The values are the same on every request: the {fake:rows} tags of a request share a sequence
  // of their own, started from one seed, so a prototype or a designer's preview looks the
  // same until its template changes. seed= gives a tag values of its own, the ones
  // padFakeRows gives for that seed - {fake:rows 'name, email', rows=5, seed=7/} is
  // padFakeRows ( 'name, email', 5, 7 ). Either way the page's own padFake sequence - its
  // padFakeSeed and what it made with it - is put back afterwards, as {pad:lorem} puts it back.

  if ( $padFakeSpec === 'rows' )
    $padFakeSpec = $padParm;

  $padFakeSpec   = trim ( (string) $padFakeSpec );
  $padFakeFields = ( $padFakeSpec === '' ) ? NULL : padFakeFields ( $padFakeSpec );

  if ( $padFakeSpec === '' ) {
    padError ( "the fake tag names its kinds - {fake:email/} or {fake:rows 'name, email', rows=5/} - of " . implode ( ', ', padFakeKinds () ) );
    return FALSE;
  }

  if ( $padFakeFields === NULL )
    return FALSE;

  // The kinds made go on the xref record, which the reference application shows: the pages
  // that make fake values of each kind.

  if ( $padInfoXref ?? FALSE )
    foreach ( $padFakeFields as [ $padFakeKind ] )
      padInfoXref ( 'fake', $padFakeKind );

  $padFakeSeedOpt = padTagParm ( 'seed', NULL );
  $padFakeSeedOpt = ( is_string ( $padFakeSeedOpt ) and ctype_digit ( $padFakeSeedOpt ) ) ? (int) $padFakeSeedOpt : $padFakeSeedOpt;
  $padFakeCount   = padTagParm ( 'rows', NULL );

  if ( $padFakeCount !== NULL and ( ! ctype_digit ( (string) $padFakeCount ) or (int) $padFakeCount > 10000 ) ) {
    padError ( "the fake tag has rows='" . padMakeSafe ( (string) $padFakeCount, 20 ) . "' - a number from 0 to 10000" );
    return FALSE;
  }

  $padFakeAsRows  = ( $padFakeCount !== NULL or $padPair [$pad] or count ( $padFakeFields ) > 1 );
  $padFakeCount ??= 1;
  $padFakeSaved   = padFakeState ();

  if ( $padFakeSeedOpt === NULL ) {
    if ( isset ( $GLOBALS ['padFakeTagState'] ) )
      padFakeState ( $GLOBALS ['padFakeTagState'] );
    else
      padFakeSeed ( 'fake' );
  }

  // rows=0 makes no row, and so no kind would read its options from the tag: one row is made
  // and dropped, the sequence put back, so a row-set of none is judged as one of some -
  // {fake:price rows=$n, from=5, to=50} with $n at 0 was an error of the strict check,
  // from= an option nothing reads, where it is the miss its @else@ shows; a misspelt
  // option is still one nothing reads.

  if ( $padFakeAsRows ) {
    $padFakeResult = padFakeRows ( $padFakeSpec, $padFakeCount, $padFakeSeedOpt, TRUE );
    if ( ! (int) $padFakeCount ) {
      $padFakeNone = padFakeState ();
      padFakeRows ( $padFakeSpec, 1, $padFakeSeedOpt, TRUE );
      padFakeState ( $padFakeNone );
    }
  } else {
    if ( $padFakeSeedOpt !== NULL )
      padFakeSeed ( $padFakeSeedOpt );
    $padFakeResult = padFake ( array_values ( $padFakeFields ) [0] [0], TRUE );
  }

  if ( $padFakeSeedOpt === NULL )
    $GLOBALS ['padFakeTagState'] = padFakeState ();

  padFakeState ( $padFakeSaved );

  // A row-set with no rows - rows=0 - is a miss, as an empty data tag is, and so is the one
  // row of a pair of one kind whose value is FALSE: {fake:boolean}yes@else@no{/fake:boolean}.

  if ( $padFakeAsRows and ! $padFakeResult )
    return FALSE;

  if ( $padFakeAsRows and padTagParm ( 'rows', NULL ) === NULL and count ( $padFakeFields ) == 1 and reset ( $padFakeResult [0] ) === FALSE )
    return FALSE;

  return $padFakeResult;

?>

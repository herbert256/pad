<?php

  // Keeps the terms of the page page= asks for - the page's size from its start - and with
  // them what the run kept beside each term - its value before the plays, the plays' answers,
  // its place in a pulled store - so the columns of a row stay those of its term.
  // inits/limits.php made the run that much longer, or whole.
  //
  // The page, its size and the number of terms the run made are booked for a {pager} after
  // the tag, as the page option books a data tag's (padPagerKeep in lib/pager.php), under the
  // names the rows answer to - the name= given, else sequence, the name of the type or the
  // store and the tag's own - and not for {resume}, which shows nothing.
  //
  // The size booked is the one the run paged with, $pqPageSize, and the page the one it showed:
  // rows= as written was booked, 10 for {prime 5, page=2}, whose page of five had a pager
  // without a next page, and PHP's cast of a rows= past the integer range ended the page.

  if ( $pqPageSize === NULL )
    return;

  $pqPageTotal = count ( $pqResult );

  // A list nothing can turn a term down from was made up to the end of the page shown only
  // (inits/limits.php, $pqPageCount): when that filled the run, the terms of the rest of it are
  // counted here, without making them, as the fixed iterator would offer them and build/one.php
  // keep them - from= to to= in steps of an increment the type did not take itself, skip= first
  // candidates left out, a FALSE or a NAN no term, and in a type's own list (fixed) the first
  // value past the integer range the end. Made one by one to count them, a range of 100,000
  // took seconds for a page of ten.

  if ( $pqPageCount and $pqPageTotal >= $pqRows and is_array ( $pqFixed ) ) {

    $pqPageList  = array_values ( $pqFixed );
    $pqPageAt    = max ( 0, (int) ceil ( $pqFrom - 1 ) );
    $pqPageLast  = min ( count ( $pqPageList ) - 1, floor ( $pqTo - 1 ) );
    $pqPageStep  = ( $pqInc > 1 and ! in_array ( 'increment', $pqDone ) ) ? (int) ceil ( $pqInc ) : 1;
    $pqPageGiven = in_array ( $pqBuild, [ 'build', 'pull', 'given' ] );
    $pqPageTries = 0;
    $pqPageTotal = 0;

    for ( ; $pqPageAt <= $pqPageLast; $pqPageAt += $pqPageStep ) {

      $pqPageOne = $pqPageList [$pqPageAt];

      $pqPageTries++;

      if ( $pqPageOne === FALSE )
        continue;

      if ( is_float ( $pqPageOne ) ) {
        if ( ! $pqPageGiven and ( $pqPageOne <= PHP_INT_MIN or $pqPageOne >= PHP_INT_MAX ) )
          break;
        if ( is_nan ( $pqPageOne ) )
          continue;
      }

      if ( $pqPageTries > $pqSkip )
        $pqPageTotal++;

    }

  }

  // Cut among the actions (actions/actions.php), the page is one of what they left: the rows
  // find their terms in the whole build by the keys actions/assoc.php gave them, so what the
  // build kept beside each term stays whole.

  $pqResult = array_slice ( $pqResult, $pqPageSkip, $pqPageSize );

  if ( ! $pqPageCut ) {
    $pqOrgHit   = array_slice ( $pqOrgHit,   $pqPageSkip, $pqPageSize );
    $pqPlaysHit = array_slice ( $pqPlaysHit, $pqPageSkip, $pqPageSize );
    $pqResultAt = array_slice ( $pqResultAt, $pqPageSkip, $pqPageSize );
  }

  if ( $pqTag == 'resume' )
    return;

  // The tag's own name is one of them, as the pager's docs have it, and the prefix it is
  // written with - other than pad:, which only says the tag is the engine's: {keep '1..30',
  // even, page=2} was booked as sequence and range only, and {pager 'keep'} after it, {pager
  // 'pull'} after {pull 's', page=2} and {pager 'make'} after {make:square page=2} found no
  // paged tag.

  $pqPageName  = $padName [$pad];
  $pqPageNames = ( is_string ( $pqNameGiven ) and $pqNameGiven !== '' ) ? [ $pqNameGiven ]
               : [ 'sequence', $pqName, $pqTag, ( $pqPrefix == 'pad' ) ? '' : $pqPrefix ];

  foreach ( array_unique ( $pqPageNames ) as $pqPageOne )
    if ( is_scalar ( $pqPageOne ) and (string) $pqPageOne !== '' ) {
      $padName [$pad] = $pqPageOne;
      padPagerKeep ( $pqPageTotal, $pqPageSize, $pqPage );
    }

  $padName [$pad] = $pqPageName;

?>

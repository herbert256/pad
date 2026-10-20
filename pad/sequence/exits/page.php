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

  if ( $pqPageSize === NULL )
    return;

  $pqPageTotal = count ( $pqResult );

  $pqResult   = array_slice ( $pqResult,   $pqPageSkip, $pqPageSize );
  $pqOrgHit   = array_slice ( $pqOrgHit,   $pqPageSkip, $pqPageSize );
  $pqPlaysHit = array_slice ( $pqPlaysHit, $pqPageSkip, $pqPageSize );
  $pqResultAt = array_slice ( $pqResultAt, $pqPageSkip, $pqPageSize );

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
      padPagerKeep ( $pqPageTotal );
    }

  $padName [$pad] = $pqPageName;

?>

<?php

  // Keeps the terms of the page page= asks for - the page's size from its start - and with
  // them what the run kept beside each term - its value before the plays, the plays' answers,
  // its place in a pulled store - so the columns of a row stay those of its term.
  // inits/limits.php made the run that much longer, or whole.
  //
  // The page, its size and the number of terms the run made are booked for a {pager} after
  // the tag, as the page option books a data tag's (padPagerKeep in lib/pager.php), under the
  // names the rows answer to - the name= given, else sequence and the name of the type or the
  // store - and not for {resume}, which shows nothing.

  if ( $pqPageSize === NULL )
    return;

  $pqPageTotal = count ( $pqResult );

  $pqResult   = array_slice ( $pqResult,   $pqPageSkip, $pqPageSize );
  $pqOrgHit   = array_slice ( $pqOrgHit,   $pqPageSkip, $pqPageSize );
  $pqPlaysHit = array_slice ( $pqPlaysHit, $pqPageSkip, $pqPageSize );
  $pqResultAt = array_slice ( $pqResultAt, $pqPageSkip, $pqPageSize );

  if ( $pqTag == 'resume' )
    return;

  $pqPageName  = $padName [$pad];
  $pqPageNames = ( is_string ( $pqNameGiven ) and $pqNameGiven !== '' ) ? [ $pqNameGiven ] : [ 'sequence', $pqName ];

  foreach ( array_unique ( $pqPageNames ) as $pqPageOne ) {
    $padName [$pad] = $pqPageOne;
    padPagerKeep ( $pqPageTotal );
  }

  $padName [$pad] = $pqPageName;

?>

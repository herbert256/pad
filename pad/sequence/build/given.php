<?php

  // Applies the build= parameter, which overrides the strategy pqBuild() inferred.
  //
  // First step of build/build.php; does nothing unless build= named a strategy. When the
  // main sequence is a stored one (pull/fixed/build/given) there is nothing to generate,
  // so the override is handed to the first play instead of $pqBuild.

  if ( ! $pqBuildName or $pqBuildName === TRUE )
    return;

  // A type built from a list of its own - kolakoski, perfect, a range or a list - is a store to
  // the run only when a play takes the name: with none, the name is the type's, where it was
  // handed to a play there is not and {sequence kolakoski, build='bool'} answered the list.

  $pqBuildPlay = ( pqStore ( $pqBuild ) and ( ! $pqSeq or count ( $pqPlays ) ) );

  // The name becomes part of an include path - build/types/<name>.php for the main
  // sequence, plays/play/<name>.php for a play - so it must name a strategy there is a file
  // for, and nothing else: with ../ in it, build= reached any .php on disk. Any other name
  // is an error, and the strategy pqBuild() inferred stays.

  $pqBuildDir = $pqBuildPlay ? 'plays/play/' : 'build/types/';

  // position is the build build='function' turns into for a type that tests values, not a name
  // to write: written, it reached eval and chance, whose terms its memo keeps for the request,
  // and {sequence eval='@*$n', build='position'} in a loop over 2 and 3 gave the multiples of
  // 2 twice.

  // The file is looked for among the names written on disk, pqNames (): asked of a file system
  // that ignores case, build='Bool' found build/types/bool.php, and build/one.php, which knows
  // the names as they are written, made no term and ended the request on "Undefined variable
  // $pq" - {sequence happy, build='Bool'}, {sequence square, build='Function'}.

  if ( ! padValidName ( $pqBuildName ) or ! isset ( pqNames ( PQ . $pqBuildDir ) ["$pqBuildName.php"] ) or $pqBuildName == 'position' ) {
    padError ( "there is no build strategy named '$pqBuildName'" );
    return;
  }

  // And the sequence it is put on has to have what the strategy runs - its own loop.php,
  // order.php, fixed.php and so on. build='order' on prime included a types/prime/order.php
  // that is not there and ended the request on the include warning, as fixed and build did on
  // the main sequence and on a play, and given looked for the list only an action's call
  // hands over. check needs no file of the type's, nor does pull for the main sequence or
  // order for a play, which reads the type's table. A store with no play to put it on leaves
  // the name with nothing to do, as before.


  $pqBuildSeq  = $pqBuildPlay ? ( reset ( $pqPlays ) ['pqSeq'] ?? '' ) : $pqSeq;
  $pqBuildFree = $pqBuildPlay ? [ 'check', 'order' ] : [ 'check', 'pull' ];

  // Two strategies stand for every type, whatever files it has. bool walks the values and
  // keeps the ones that belong: a type without a bool.php is asked through its membership
  // check, as keep asks it. function makes the term at each position: a type without a
  // formula of its position makes it the way it builds its terms, and a type that tests values
  // - prime, happy, triangular - through the position build, the nth value its test accepts
  // (pqPosition in lib/sequence.php). Both ended the request on "has no build strategy" for a
  // type without the file.

  if ( $pqBuildSeq and $pqBuildName == 'bool' and ! padFileExists ( PT . "$pqBuildSeq/bool.php" ) )

    $pqBuildName = 'check';

  elseif ( $pqBuildSeq and $pqBuildName == 'function' and ! padFileExists ( PT . "$pqBuildSeq/function.php" ) ) {

    $pqBuildName = pqBuild ( $pqBuildSeq, 'loop' );

    // A loop that tests the values - emirp, multiple, with a bool.php beside it - walks values
    // too, and takes the position build as a bool type does: build='function' on emirp gave
    // the emirps from the value 3 on, 13 17, where the 3rd and 4th are 31 37. And so does a
    // type that steps its walk itself, pqStepType (): step=3 with build='function', from=2,
    // to=4 gave the value 2, where its 2nd to 4th terms are 4 7 10, and a play of step, even or
    // odd was its loop or its nudge to the next member, even over 2 3 4 answering 2 4 4 for
    // 4 6 8.

    if ( $pqBuildName == 'bool' or ( $pqBuildName == 'loop' and padFileExists ( PT . "$pqBuildSeq/bool.php" ) )
                                or pqStepType ( $pqBuildSeq ) )
      $pqBuildName = 'position';

  }

  // A walk of the values tests every candidate the tag asked for, as inits/check/sequence.php
  // has it for keep, remove and flag, and a walk of the positions offers every position it
  // was asked for: the window the type's init.php reshaped for its own generation - even
  // doubles from and to, multiple steps by its parameter - goes back to what was given.

  if ( ! $pqBuildPlay and in_array ( $pqBuildName, [ 'check', 'bool', 'position' ] ) ) {
    $pqFrom = $pqInitFrom ?? $pqFrom;
    $pqTo   = $pqInitTo   ?? $pqTo;
    $pqInc  = $pqInitInc  ?? $pqInc;
  }

  $pqBuildFree [] = 'position';

  if ( $pqBuildSeq and ! in_array ( $pqBuildName, $pqBuildFree )
                   and ! isset ( pqNames ( PT . "$pqBuildSeq/" ) ["$pqBuildName.php"] ) ) {
    padError ( "the $pqBuildSeq sequence has no build strategy named '$pqBuildName'" );
    return;
  }

  if ( $pqBuildPlay ) {

    foreach ( $pqPlays as $padK => $padV ) {
      $pqPlays [$padK] ['pqBuild'] = $pqBuildName;
      return;
    }

  } else

    $pqBuild = $pqBuildName;

?>

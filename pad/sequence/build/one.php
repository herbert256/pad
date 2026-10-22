<?php

  // Walks the candidates of a build and turns each into at most one result term - the heart
  // of the build.
  //
  // Included once a walk by both iterators, which set the walk up and name it in $pqWalk:
  // 'loop' (build/types/type/loop.php) counts $pqGo from $pqFrom to $pqTo in steps of $pqInc,
  // 'fixed' (build/types/type/fixed.php) offers the entries of the list in $pqFixed. Each
  // candidate is settled in the do block below with the candidate in $pqLoop: a break there
  // goes on to the next candidate, a break 2 ends the whole build - try limit exhausted,
  // stop= value reached, rows= filled, or a float out of int range.
  //
  // Refreshes a random or store-driven parm, optionally replaces $pqLoop with a random
  // pick, then produces $pq using the strategy named by $pqBuild. Plays get to filter or
  // rewrite $pq next, then minimal/maximal, unique and skip are applied. Accepted terms go
  // to $pqResult, with the pre-plays value in $pqOrgHit and each play's own answer in
  // $pqPlaysHit, which sequence/exits/extra/ exposes as extra fields on the tag's data.
  // An order build also appends every generated term to $pqOrder before any filter, since
  // later terms are computed from earlier ones, and suppresses the terms before from=.
  //
  // The walk was the iterators' own loop, which included this file for every candidate - and
  // an include costs what binding every variable the file names to the scope costs, on the
  // way in and again on the way out: some 320 ns a candidate for the 38 names here, three
  // quarters of what a {sequence '1..50000', count} took, so every variable a fix added here
  // made every candidate of every run slower - the bench pages of small sequences lost 9 to
  // 12% that way. Walked here, {sequence '1..50000', count} takes 7.6 ms where it took 21.9.

  $pqWalkList  = ( $pqWalk == 'fixed' );
  $pqWalkTerms = $pqWalkList ? $pqFixed : [];
  $pqWalkEnd   = count ( $pqWalkTerms );
  $pqWalkAt    = -1;

  // What is asked of every candidate and holds for the whole walk is settled before it, as the
  // build, its plays and its pull stay what they are while it walks: whether the build hands
  // the candidate over as it is ($pqWalkStore), whether the walk is held to the time a run has
  // ($pqWalkClock, see below), whether its values are given ($pqGiven, below), and the function
  // a bool or a function build calls. Worked out for every candidate - pqStore () twice, two
  // in_array () and the name made with ucfirst () - they took a tenth of a candidate's time.

  $pqWalkStore = pqStore ( $pqBuild );
  $pqWalkClock = ( count ( $pqPlays ) or ( ! $pqPull and ! in_array ( $pqBuild, [ 'fixed', 'given', 'build' ] ) ) );
  $pqWalkCall  = ( $pqBuild == 'bool' ) ? 'pqBool' . ucfirst ( $pqSeq ) : ( ( $pqBuild == 'function' ) ? 'pq' . ucfirst ( $pqSeq ) : '' );
  $pqGiven     = in_array ( $pqBuild, [ 'build', 'pull', 'given' ] );

  // A type that names its next member - pqNextAntiprime, from the list of the highly composite
  // numbers - is walked from one member to the next instead of over every value between them:
  // the candidates are the members, each tested as before, and the try limit counts them. Over
  // every value antiprime stopped at its 20th term, 7560, as the 10,000 candidates ran out, and
  // {sequence antiprime, rows=25} gave 20. Only a walk of every value one at a time takes it:
  // try= and skip= count the values, increment= and a random draw choose them, and keep the
  // walk they had.

  $pqWalkNext  = ( $pqBuild == 'bool' and ! $pqWalkList and $pqInc == 1 and ! $pqRandomInc and ! $pqRandomly
                   and ! $pqSkip and ! isset ( $pqParms ['try'] ) and function_exists ( 'pqNext' . ucfirst ( $pqSeq ) ) )
               ? 'pqNext' . ucfirst ( $pqSeq ) : '';

  while ( TRUE ) {

    if ( $pqWalkList ) {

      // The next entry of the list, as a foreach over it as it stood when the walk started. An
      // increment greater than 1 skips that many entries after each candidate; from/to select a
      // 1-based slice of the list.

      if ( ++$pqWalkAt >= $pqWalkEnd )
        break;

      $pqKey  = $pqWalkAt;
      $pqLoop = $pqWalkTerms [$pqWalkAt];

      if ( $pqSkipNow ) {
        $pqSkipCount++;
        if ( $pqSkipCount < $pqInc )
          continue;
        $pqSkipCount = $pqSkipNow = 0;
      }

      if ( $pqKey < $pqFrom - 1 ) continue;
      if ( $pqKey > $pqTo - 1   ) break;

      $pqAt = $pqKey;

    } else {

      // The next value of the counter, while it is within to=. A counter that is no finite
      // number is within nothing: tested by the negated comparison alone, a NAN went through
      // under opcache, which reads ! ( $pqGo <= $pqTo ) as $pqGo > $pqTo - false for a NAN -
      // where the command line stopped, and multiple=1e400, whose start is 0 * INF, walked
      // NAN candidates over the web up to the try limit: a million with to=, 2.7 seconds.

      if ( ! ( $pqGo <= $pqTo ) or ( is_float ( $pqGo ) and ! is_finite ( $pqGo ) ) )
        break;

      $pqLoop = $pqGo;
      $pqAt   = NULL;

    }

    do {

      $pqTries++;

      // Out of candidates, or out of time: $padSeqMaxSeconds (config/sequence.php) ends a run
      // still looking as the candidate cap does - a costly test reached a million candidates in
      // minutes, past PHP's time limit, and the page died with a 500 instead. A pulled store and
      // a fixed or given list end with their data, as inits/limits.php leaves their candidates
      // unbounded, so time does not cut them either - nor a range or a list a type builds whole
      // ('build'), which inits/limits.php gives all its rows as it gives a given list: cut, a
      // slow request answered {sequence '1..200000', count} with the values it reached. A range
      // with a play is held to the budget still: each of its terms costs what the play costs,
      // and {sequence '1..4000', make, fibonacci} ran 22 seconds, a longer range into PHP's time
      // limit and a 500.

      // A pulled store, a fixed or a given list with a play is held to it as the range is: with
      // {pull:s make, golomb} over 2000 stored positions the play ran 11 to 17 seconds.

      // Out of time is marked apart, $pqTimeOut, so a position lookup that could not reach its
      // term says which ended it: the clock was reported as the candidate cap, and
      // sequence:strong(5) said "the sequence gives 3 terms within 1000000 candidates" where the
      // five seconds had ended the walk before 40585, the fourth.

      if ( $pqTries > $pqTry ) {
        $pqTriesOut = TRUE;
        break 2;
      }

      if ( $pqWalkClock and hrtime ( TRUE ) > $pqTriesUntil ) {
        $pqTriesOut = $pqTimeOut = TRUE;
        break 2;
      }

      // Full before a candidate is even made - a count of 0 asks for no rows at all.

      if ( count ( $pqResult ) >= $pqRows )
        break 2;

      if ( $pqRandomParm ) $pqParm = include PQ . 'build/parm.php';
      if ( $pqParmStore  ) $pqParm = include PQ . 'build/store.php';

      // A parameter store that has run out ends the run - see build/store.php, and plays.php
      // for the same in a play.

      if ( $pqParmStore and $pqParm === NULL )
        break 2;

      if ( ( $pqRandomParm or $pqParmStore ) and ! pqParmTerm ( $pqSeq, $pqParm ) )
        break 2;
      if ( $pqRandomly   ) $pqLoop = include PQ . 'build/randomly/randomly.php';

      // The position in the list a term comes from - the fixed iterator's, or the one a random
      // draw from a store picked - kept beside the term in $pqResultAt, so the columns and the
      // destructive actions of a pull know which entry of the store each term is.

      if ( $pqRandomly and $pqWalkStore )
        $pqAt = $pqRandomlyRand;

      // A function type computes the term at a position, and a position is a whole number: at
      // from=2.5 bell read the undefined row 2.5 of its triangle and ended the request. It is
      // handed as the integer it is, however written: from=3.0, '+3' or '3e0' reached bell and
      // newmanConway as the text, and the row or term they kept under 3 was read under "3.0".

          if ( $pqWalkStore           )  $pq = $pqLoop;
      elseif ($pqBuild == 'bool'    )  $pq = $pqWalkCall ( $pqLoop, $pqParm );
      elseif ($pqBuild == 'function')  $pq = pqBoolWhole ( $pqLoop ) ? $pqWalkCall ( (int) ( $pqLoop + 0 ) ) : FALSE;
      elseif ($pqBuild == 'position')  $pq = pqBoolWhole ( $pqLoop ) ? pqPosition ( $pqSeq, $pqParm, (int) ( $pqLoop + 0 ) ) : FALSE;
      elseif ($pqBuild == 'check'   )  $pq = include PQ . "build/mode.php";
      elseif ($pqBuild == 'loop'    )  $pq = include PT . "$pqSeq/loop.php";
      elseif ($pqBuild == 'make'    )  $pq = include PT . "$pqSeq/make.php";
      elseif ($pqBuild == 'order'   )  $pq = include PT . "$pqSeq/order.php";

      if     ( $pq === FALSE ) break;
      elseif ( $pq === TRUE  ) $pq = $pqLoop;

      // A term out of the integer range ends the run as soon as it is made, before a play is
      // handed it: checked only after the plays, keep even was asked about the overflowed float
      // and {sequence fibonacci, keep, even, rows=50} ended the request on "not representable as
      // an int". 2^63 is out of range too, though as a float it compares equal to PHP_INT_MAX -
      // tested with >, {sequence power=2, rows=70} ended on 9.2233720368548E+18. A play can make
      // such a value as well, so the same test follows the plays.
      //
      // A value a list, a store or an action's array hands over is given, not computed: it is
      // passed on as it was written, where 1e20 in a list ended the run there - only a play
      // over it computes a term. $pqGiven, settled before the walk, says which.

      // -2^63 as a float is an overflow too, a term rounded to it, as 2^63 is on the other side:
      // tested with <, subtract=9223372036854775800 from=-10 printed -9.2233720368548E+18 twice.
      // A random draw past the range is a draw lost, not the end of the run - every later draw
      // may fit, and {sequence power=2, randomly, to=100, rows=5} gave 0 to 5 rows.

      if ( ! $pqGiven and is_float ( $pq ) and ( $pq <= PHP_INT_MIN or $pq >= PHP_INT_MAX ) ) {
        if ( $pqRandomly ) break;
        break 2;
      }

      // A term that is no number at all - the square root exponentiation=0.5 takes of a negative
      // value - is no term: printed, PHP ended the request on the NAN coerced to a string. Not a
      // play's answer either, below.

      if ( is_float ( $pq ) and is_nan ( $pq ) ) break;

      $pqOrgSet = $pq;

      // An order build computes each term from the ones before it, so every generated term goes
      // into its history now, before a play or a filter can turn it down: added only once
      // accepted, a rejected term left a hole the next one read - {sequence fibonacci, keep,
      // even} ended on an undefined key.

      if ( $pqBuild == 'order' ) {

        $pqOrder [] = $pqOrgSet;

        if ( count ( $pqOrder ) > $pqOrderMost ) {
          $pqTriesOut = TRUE;
          break 2;
        }

        // A position offered between two whole ones - a fractional increment= - has no term, and
        // the next one is offered instead.

        while ( $pqOrderNext < $pqLoop )
          $pqOrderNext += $pqOrderRandom ? max ( 1, (int) pqRandomParm3 ( $pqOrderRandom ) ) : $pqOrderInc;

        if ( $pqLoop != $pqOrderNext ) {
          $pqTries--;
          break;
        }

        $pqOrderNext += $pqOrderRandom ? max ( 1, (int) pqRandomParm3 ( $pqOrderRandom ) ) : $pqOrderInc;

      }

      if ( count ( $pqPlays ) ) {
        include PQ . 'plays/plays.php';
        if ( $pq === FALSE ) {
          if ( $pqPlaysOut ) break 2;
          break;
        }
      }

      // minimal=, maximal= and stop= count only when given: their defaults, PHP_INT_MIN and
      // PHP_INT_MAX, stand for none, and tested as bounds they turned down a listed value past
      // them - 100000000000000000000 - and ended the list on 9223372036854775807 or 2^63, so
      // {sequence '9223372036854775808;1'} showed its first value alone.

      if ( ( ! $pqGiven or $pq !== $pqOrgSet ) and is_float ($pq) and ( $pq <= PHP_INT_MIN or $pq >= PHP_INT_MAX ) ) {
        if ( $pqRandomly ) break;
        break 2;
      }

      if ( is_float ($pq)   and is_nan ( $pq )   ) break;
      if ( $pqMin !== PHP_INT_MIN and is_numeric ($pq) and $pq < $pqMin ) break;
      if ( $pqMax !== PHP_INT_MAX and is_numeric ($pq) and $pq > $pqMax ) break;
      // unique keeps a set of the values kept, $pqUniqueSeen: looked up in $pqResult, every
      // term was compared with all the terms before it, and {sequence '1..200000', unique,
      // count} took more than the time a run has and answered 142941.

      if ( $pqUnique ) $pqUniqueKey = pqValueKey ( $pq );

      if ( $pqUnique and isset ( $pqUniqueSeen [$pqUniqueKey] ) ) break;
      if ( $pqSkip and $pqTries <= $pqSkip )                    break;

      // The first pass of randomly over a type built by bool, function or order, with its plays
      // put aside - build/randomly/build/inits.php, which arms $pqRandomlyStop with the stop= put
      // aside with them - ends where the ordered run with the plays ends: they are run over the
      // term for that test alone, before the term is added as they are, and the pass ends on the
      // term they make reaching stop=, or on a play that ends the run. Without it the window was
      // the default try count, nearly every term of it past stop= once played, so the second pass
      // ended on its first draw - {sequence happy, randomly, stop=50, make, add=10} was one value
      // out of 10,000, as was add=0, which changes nothing.

      if ( $pqRandomlyStop !== PHP_INT_MAX ) {
        $pqRandomlyKeep    = $pq;
        $pqPlays           = $pqPlaysRandomly;
        include PQ . 'plays/plays.php';
        $pqPlays           = [];
        $pqRandomlyStopped = ( $pqPlaysOut or ( is_numeric ( $pq ) and $pq >= $pqRandomlyStop ) );
        $pq                = $pqRandomlyKeep;
      }

      $pqResult   [] = $pq;
      $pqOrgHit   [] = $pqOrgSet;
      $pqResultAt [] = $pqAt ?? NULL;

      if ( $pqUnique )
        $pqUniqueSeen [$pqUniqueKey] = TRUE;

      if ( count ( $pqPlays ) )
        $pqPlaysHit [] = $pqPlaysSet;

      if ( $pqStop !== PHP_INT_MAX and is_numeric ($pq) and $pq >= $pqStop ) break 2;
      if ( $pqRandomlyStop !== PHP_INT_MAX and $pqRandomlyStopped ) break 2;
      if ( count($pqResult) >= $pqRows )              break 2;

    } while ( FALSE );

    // A random increment (increment=a...b) is drawn again after every candidate.

    if ( $pqRandomInc )
      $pqInc = pqRandomParm3 ( $pqRandomInc );

    if ( $pqWalkList ) {

      // Entries are skipped for an increment above 1, unless the sequence type took the
      // increment itself.

      if ( $pqInc > 1 and ! in_array ( 'increment', $pqDone ) )
        $pqSkipNow = 1;

    } else {

      // Out past the integer range a float no longer moves when the step is added - -1e19 + 1
      // is -1e19 - and the walk offered the same candidate again until the try limit, so
      // {sequence modulo=7, from=-10000000000000000000, rows=3} answered -3 -3 -3. A walk that
      // does not advance has nothing more to offer. A random increment drawn as 0 stands still
      // by intent, and is no such walk: ended there, increment='0...2' answered a term or two
      // where it asked for eight.

      if ( $pqInc >= 1 and $pqGo + $pqInc <= $pqGo )
        break;

      $pqGo = $pqGo + $pqInc;

      if ( $pqWalkNext )
        $pqGo = $pqWalkNext ( $pqGo );

    }

  }

?>

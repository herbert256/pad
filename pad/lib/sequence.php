<?php

  // Shared helpers for the sequence subsystem (pad/sequence/, prefix pq*), used by the
  // {sequence} tag, its actions and the individual sequence types.
  //
  // Which sequence, which variant:
  //   pqSeq / pqAction  does a type directory PT/<name> or an action PA/<name>.php exist
  //   pqBuild           picks the file inside a type directory to run - the named one, or
  //                     loop, make, function, bool, order, build, fixed, generated
  //   pqStore / pqPlay  classify a keyword as a store (pull, fixed, build, given) or a
  //                     play operation (make, keep, remove, flag)
  //
  // Parameters: pqCorrectParms spreads a pipe-separated parameter over the three sequence
  // parameters; pqRandomParm and pqRandomParm3 turn "a..b" and "a...b" into a random value
  // in that range, both through pqRandomBetween, which takes the two ends in either order -
  // "9..2" is the same range as "2..9" rather than an error; pqActionArray takes an action's
  // first parameter, unwrapping a single nested array; pqDone removes a handled option from
  // the option list.
  //
  // Picking and reordering: pqRandom with pqRandomKeys and pqRandomDups draw $count
  // members, with or without duplicates and in original or shuffled order; pqShuffle
  // shuffles while keeping keys; pqRandomLy picks a random step in a loop; pqTruncate
  // cuts members off either end, and cuts nothing for a count below 1 - taking 0 off the
  // right would otherwise slice to 0 and leave nothing at all; padTypeReverse reverses a
  // number's digits.
  //
  // pqArray goes the other way round: it hands PHP the terms of a type as an array, made as
  // pqTerms makes them.

  function pqActionArray ( &$parms ) {

    $pqFirst = array_shift ( $parms );

    if ( ! is_array ( $pqFirst ) ) {

      return $pqFirst;

    } elseif ( is_array ( $pqFirst ) ) {

      $pqFirst = array_values ( $pqFirst );

      if ( count ( $pqFirst ) == 1 and is_array ( $pqFirst [0] ) )
        $pqFirst = $pqFirst [0];

    }

    return $pqFirst;

  }

  function pqRandomParm ( &$parm ) {

    if ( ! is_string ( $parm ) )
      return;

    if     ( str_contains ( $parm, '...' ) ) $split = '...';
    elseif ( str_contains ( $parm, '..'  ) ) $split = '..';
    else                                     return;

    padSplit ( $split, $parm, $from, $to );

    if ( is_numeric ( $from ) and is_numeric ( $to ) )
      $parm = pqRandomBetween ( $from, $to );

  }

  function pqRandomParm3 ( $parm ) {

    padSplit ( '...', $parm, $from, $to );

    if ( is_numeric ( $from ) and is_numeric ( $to ) )
      return pqRandomBetween ( $from, $to );
    else
      return $parm;

  }

  // An end past the integer range is as far as the range goes, as from= and to= past it are
  // (inits/limits.php): PHP's (int) of '1e400' is 0, so '1...1e400' drew from 0..1 - a 0 out
  // of the range asked - and '-1e400...-5' from -5..0.

  function pqRandomBetween ( $from, $to ) {

    $from = ( $from >= PHP_INT_MAX ) ? PHP_INT_MAX : ( ( $from <= PHP_INT_MIN ) ? PHP_INT_MIN : (int) $from );
    $to   = ( $to   >= PHP_INT_MAX ) ? PHP_INT_MAX : ( ( $to   <= PHP_INT_MIN ) ? PHP_INT_MIN : (int) $to   );

    if ( $from <= $to )
      return mt_rand ( $from, $to );
    else
      return mt_rand ( $to, $from );

  }

  // A run parameter that has to be a number - from=, to=, rows= and the rest - and is none:
  // the strict check names it, and the lenient walk carries on with its default. One that is
  // empty is one not given and takes its default under both: rows=$rows from a form field
  // left blank - ?page&rows= - rendered the default rows, and the strict check had made it
  // the error "rows= takes a number, not ''".

  function pqNumber ( $name, &$value, $default ) {

    if ( is_numeric ( $value ) or $value === $default )
      return;

    if ( $value === '' or $value === NULL ) {
      $value = $default;
      return;
    }

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( "$name= takes a number, not '"
               . ( is_scalar ( $value ) ? ( is_bool ( $value ) ? '' : $value ) : gettype ( $value ) ) . "'" );

    $value = $default;

  }

  function pqShuffle ( &$array ) {

    $shuffled = [];
    $keys     = array_keys ( $array );

    shuffle ( $keys );

    foreach ( $keys as $key )
      $shuffled [$key] = $array [$key];

    $array = $shuffled;

  }

  // A type is a directory of PT named by a plain name: with ../ in it the name reached any
  // directory on disk, and the type's files were included from there. A letter, then letters
  // and digits, as every type is named: '.' passed padValidName, and PT . '.' is the types
  // directory itself, so keep='.' ended the request on the missing build/types/unknown.php.

  function pqSeq ( $seq  ) {

    if ( is_string ( $seq ) and preg_match ( '/^[A-Za-z][A-Za-z0-9]*$/', $seq ) and isset ( pqNames ( PT ) [$seq] ) )
      return TRUE;
    else
      return FALSE;

  }

  // pqArray ( 'even', '', 'rows=3' ) is [ 2, 4, 6 ]: the terms of a type of types/ as the
  // values they are. The parameter is a value of the type, whatever text is in it, and the
  // options are the run's named limits - [ 'rows' => 3, 'to' => 50 ] or the same written
  // 'rows=3, to=50', each value as written - made by pqTerms, nothing rendered. It wrote a
  // {sequence} tag pair of the three and rendered it through padCode, so the parameter was
  // template source - pqArray ( 'repeat', "1, make, eval='php:abs(-123)'", 'rows=1' ) ran the
  // PHP call and answered [ '123' ] - and a term came back as the page writes it, 'a&b' as
  // 'a&amp;b' and 'a,b' with the comma one of the template's stand-ins. A name that is no type
  // or an option the run has no use for is reported, and the answer is no terms.

  function pqArray ( $sequence, $parm='', $options=[] ) {

    if ( ! pqSeq ( $sequence ) ) {
      padError ( "pqArray: there is no sequence type named '" . padMakeSafe ( $sequence, 30 ) . "'" );
      return [];
    }

    if ( is_string ( $options ) ) {

      $list = [];

      foreach ( padExplode ( $options, ',' ) as $option ) {

        [ $name, $value ] = array_map ( 'trim', explode ( '=', $option, 2 ) ) + [ 1 => TRUE ];

        if ( is_string ( $value ) and preg_match ( '/^([\'"])(.*)\1$/s', $value, $quoted ) )
          $value = $quoted [2];
        elseif ( is_numeric ( $value ) )
          $value += 0;

        $list [$name] = $value;

      }

      $options = $list;

    }

    if ( ! is_array ( $options ) )
      $options = [];

    $limits = [ 'from', 'to', 'sole', 'minimal', 'maximal', 'min', 'max', 'increment', 'rows',
                'try', 'stop', 'skip', 'randomly', 'unique', 'build' ];

    foreach ( array_keys ( $options ) as $name )
      if ( ! in_array ( $name, $limits, TRUE ) ) {
        padError ( "pqArray: '" . padMakeSafe ( $name, 30 ) . "' is no option it takes - " . implode ( ', ', $limits ) );
        return [];
      }

    return pqTerms ( $sequence, ( $parm === TRUE ) ? '' : $parm, $options );

  }

  // pqTerms makes the terms of a type - for pqArray, and for the engine itself to look in:
  // the type and its parameter go to sequence/terms.php as data, the run is bounded by
  // $parms - stop, to, sole, rows - and the terms come back as they were made, nothing
  // rendered. A parameter of nothing is the bare type.

  //
  // $until, an hrtime () moment, is the deadline of the run in place of the $padSeqMaxSeconds
  // of its own every run has - for a walk made in stretches that share one (pqCheckWalk).

  function pqTerms ( $sequence, $parm, $parms, $until = NULL ) {

    $pqSetAction = $sequence;
    $pqSetParm   = ( $parm === FALSE or $parm === NULL or $parm === '' ) ? TRUE : $parm;
    $pqSetParms  = $parms;
    $pqSetUntil  = $until;

    return include PQ . 'sequence/terms.php';

  }

  // The value at position $n of a type whose terms are the values its bool.php test accepts -
  // what build='function' gives such a type, the position build. The values are made by the
  // type's own build, walked from 1, and kept for the request per type and parameter; asked
  // past what was made, the walk is extended as pqTermsCount () has it - so a walk to the
  // ceiling is made only for a position past the last term there is.
  // A position the walk cannot
  // reach in the candidates and the time a run has answers a float past the integer range,
  // which ends the run as an overflowed term does - strong has four terms, and asked for a
  // fifth it would walk to the ceiling for every position after it.

  function pqPosition ( $seq, $parm, $n ) {

    static $made = [], $steps = [];

    if ( $n < 1 )
      return FALSE;

    $key = $seq . '|' . pqCheckParm ( $parm );

    // A type that steps its walk itself - even, odd, step, multiple: pqStepType () - over a
    // number or none makes whole terms a fixed step apart, so the term at a position is worked
    // out from its first three: walked by count, a position past the ceiling of candidates was
    // past the walk, and {sequence even, build='function', from=1000000, rows=3} gave 2000000
    // alone after a second, from=3000000 nothing. A step past the integer range answers the
    // float an overflow does.

    if ( pqStepType ( $seq ) and ( is_numeric ( $parm ) or in_array ( $parm, [ TRUE, FALSE, NULL, '' ], TRUE ) ) ) {

      $steps [$key] ??= pqTerms ( $seq, $parm, [ 'rows' => 3 ] );

      [ $one, $two, $three ] = $steps [$key] + [ NULL, NULL, NULL ];

      if ( is_int ( $one ) and is_int ( $two ) and is_int ( $three ) and $three - $two == $two - $one )
        return $one + ( $n - 1 ) * ( $two - $one );

    }

    $made [$key] = pqTermsCount ( $seq, $parm, $n, $made [$key] ?? [ 'terms' => [], 'end' => '', 'next' => NULL, 'tries' => 0 ] );

    return $made [$key] ['terms'] [$n - 1] ?? (float) PHP_INT_MAX * 2;

  }

  // The terms of a type as far as the $n-th, asked by count with the ceiling of candidates -
  // what pqPosition () and pqNth () look a position up in - from what was made, $made as
  // pqTermsMade () holds it, extended when $n is past its terms and its walk did not come up
  // short. A type built by bool walks the values from 1 and its terms are the ones its test
  // accepts, so its walk goes on from the candidate after the last term for the terms missing
  // only, with what is left of the candidates, as sequence/sequence.php does for a lookup; any
  // other walk is made again for twice as many. Made again from 1 for every doubling, each walk
  // with the five seconds of its own, {sequence '1..100', make, amicable, build='order'} took
  // seven seconds, past the run's five, and its count was where the clock stopped the last.

  function pqTermsCount ( $seq, $parm, $n, $made ) {

    if ( count ( $made ['terms'] ) >= $n or $made ['end'] !== '' )
      return $made;

    $most = $GLOBALS ['padSeqMaxTries'] ?? 1000000;

    if ( $made ['next'] !== NULL ) {
      $want  = $n - count ( $made ['terms'] );
      $left  = $most - $made ['tries'];
      $terms = ( $left > 0 ) ? pqTerms ( $seq, $parm, [ 'rows' => $want, 'from' => $made ['next'], 'try' => $left ] ) : [];
      $all   = array_merge ( $made ['terms'], $terms );
    } else {
      $want  = max ( $n, 2 * count ( $made ['terms'] ) );
      $terms = $all = pqTerms ( $seq, $parm, [ 'rows' => $want, 'try' => $most ] );
    }

    $short = ( count ( $terms ) < $want );
    $last  = end ( $all );
    $walk  = ( ! $short and is_int ( $last ) and pqBuild ( $seq, 'loop' ) == 'bool' );

    return [ 'terms' => $all, 'end' => $short ? 'short' : '', 'next' => $walk ? $last + 1 : NULL, 'tries' => $walk ? $last : 0 ];

  }

  // Whether a type with a formula of its position makes its terms no further than the ceiling
  // a run walks to, $padSeqMaxTries - prime, recaman, newmanConway and vanEck make them in
  // order and answer a position past it with the float past the integer range an overflow
  // answers. Asked for its first term under a ceiling of 0, such a type answers that float
  // too, where a type that only overflows answers the term.

  function pqCeilingType ( $seq ) {

    static $types = [];

    if ( isset ( $types [$seq] ) )
      return $types [$seq];

    $ceiling = $GLOBALS ['padSeqMaxTries'] ?? 1000000;

    $GLOBALS ['padSeqMaxTries'] = 0;
    $first = ( 'pq' . ucfirst ( $seq ) ) ( 1 );
    $GLOBALS ['padSeqMaxTries'] = $ceiling;

    return $types [$seq] = ( is_float ( $first ) and $first >= PHP_INT_MAX );

  }

  // What a name is already, when a store may not take it: a number, a type, a sequence tag, a
  // sequence option, an action or a PAD option - the resolver in lib/type.php could no longer
  // tell the store from it, and a store named 3 took the place of the number 3 as a
  // parameter: after push='3', add=3 added the store's terms and slice=3 ended the request on
  // array_slice's TypeError. '' when the name is free.

  function pqStoreTaken ( $name ) {

    if     ( is_numeric ( $name )                               ) return 'a number';
    elseif ( pqSeq ( $name )                                    ) return 'a Sequence name';
    elseif ( padFileExists ( PQ . "start/tags/$name.php" )      ) return 'a Sequence tag';
    elseif ( padFileExists ( PQ . "options/types/$name.php" )   ) return 'a Sequence option name';
    elseif ( pqAction ( $name )                                 ) return 'an Action name';
    elseif ( padFileExists ( PAD . "options/$name.php" )        ) return 'a PAD option name';

    return '';

  }

  // What the page made under a name already, which a sequence store of that name would take the
  // place of: lib/type.php padTypeTag reaches $pqStore before every other store and before a
  // page variable, so {name} iterated the sequence from then on. The sequence pair that only
  // names its loop - {sequence 3, name='orders'} - made a later {orders}{$id}{/orders} loop
  // over 1 2 3 instead of the page's orders, and push='x' after a {data 'x'}, {content 'x'},
  // {bool 'x'} or {define 'x'} left that store reachable as {data:x} only, in silence, where a
  // {data 'x'} after the sequence store is refused (tags/_go/store.php). '' when it made none.

  function pqStoreHeld ( $name ) {

    global $padBoolStore, $padContentStore, $padDataStore, $padDefineStore, $padMacroStore, $padSelect;

    if ( ! is_string ( $name ) and ! is_int ( $name ) )
      return '';

    if     ( isset ( $padDataStore    [$name] ) ) return 'a data store';
    elseif ( isset ( $padContentStore [$name] ) ) return 'a content block';
    elseif ( isset ( $padBoolStore    [$name] ) ) return 'a bool store';
    elseif ( isset ( $padDefineStore  [$name] ) ) return 'a {define} tag';
    elseif ( isset ( $padMacroStore   [$name] ) ) return 'a {macro}';
    elseif ( isset ( $padSelect       [$name] ) ) return 'a select table';
    elseif ( is_array ( pqStorePageValue ( $name ) ) and padValidStore ( $name ) ) return 'an array of the page';

    return '';

  }

  // What the page itself holds under a name, for pqStoreHeld: not the field of a row a loop
  // around published - padSetGlobalOcc put aside what that row shadowed, and the outermost
  // level that did holds the page's own value, while one that made the name from nothing says
  // the page has none - and not a value the request brought (padGetParms notes it in
  // $padRequestGot), unless the page has set it since. With $padRequestVars TRUE every GET and
  // POST value is a global, a list too: ?primes[]=1 made every page that pushes a store primes
  // answer 500, a form posting a checkbox list nums[] refused its own post on a page pushing
  // nums, and a row whose field kids held a list refused push='kids' inside its loop. The
  // store wins over such a value, as it did before the refusal was there.

  function pqStorePageValue ( $name ) {

    global $pad, $padSaveOcc, $padDeleteOcc, $padRequestGot;

    $value = $GLOBALS [$name] ?? NULL;

    for ( $i = 1; $i <= $pad; $i++ )
      if ( array_key_exists ( $name, $padSaveOcc [$i] ?? [] ) ) {
        $value = $padSaveOcc [$i] [$name];
        break;
      } elseif ( in_array ( $name, $padDeleteOcc [$i] ?? [], TRUE ) )
        return NULL;

    if ( isset ( $padRequestGot [$name] ) and $value === $padRequestGot [$name] )
      return NULL;

    return $value;

  }

  // The key the terms of a position lookup - sequence:prime(5) - are kept under, or '' when
  // they cannot be: a type that draws at random or evaluates an expression gives other terms
  // another time, a parameter written as a range is drawn per term, and one naming a store
  // follows what the store holds now. {info} records every run, so under it nothing is kept.

  function pqTermsKey ( $seq, $build, $parm ) {

    if ( $GLOBALS ['padInfo'] ?? FALSE )
      return '';

    if ( in_array ( $seq, [ 'random', 'chance', 'eval' ] ) )
      return '';

    if ( ! is_scalar ( $parm ) )
      return '';

    if ( is_string ( $parm ) and ( str_contains ( $parm, '..' ) or isset ( $GLOBALS ['pqStore'] [$parm] ) ) )
      return '';

    return "$seq|$build|" . pqCheckParm ( $parm );

  }

  // What was made under a key of pqTermsKey (): the terms, and as far as the walk got - 'end',
  // why it came up short of what it was asked, '' when it did not; 'next', the candidate a
  // walk of the values goes on from, and 'tries', the candidates it has tested. With $made
  // given, keeps that when it holds as many terms as were kept or more.

  function pqTermsMade ( $key, $made = NULL ) {

    static $kept = [];

    if ( $made !== NULL and count ( $made ['terms'] ) >= count ( $kept [$key] ['terms'] ?? [] ) )
      $kept [$key] = $made;

    return $kept [$key] ?? [ 'terms' => [], 'end' => '', 'next' => NULL, 'tries' => 0 ];

  }

  // The names in a directory of the subsystem - the types in PT, the actions in PA - as they
  // are written on disk, read once a request. A type or an action is named exactly: asked of
  // the file system, a case-insensitive one - macOS, Windows - took {sequence Prime} for the
  // prime type, included types/Prime/bool.php after types/prime/bool.php and ended the request
  // on "Cannot redeclare function pqBoolPrime()", and sequence:Fibonacci(8) answered 13 where
  // a Linux server says there is no such type.

  function pqNames ( $dir ) {

    static $names = [];

    return $names [$dir] ??= array_flip ( array_diff ( scandir ( $dir ) ?: [], [ '.', '..' ] ) );

  }

  // Whether the parameter a run hands a type for one term - a term of a store named as the
  // parameter, a draw of a range written as it - is one the type can take: a number, or the
  // bare type's TRUE, for a type whose init.php asks for a number (inits/number.php). That
  // check looks at the parameter as written, once, and lets a store's name and a range
  // through; the terms they hand over were taken as they came, and add='s' over a store
  // holding a word, or add='a..z', ended the request on "Unsupported operand types". FALSE,
  // after the error, ends the run at that term.

  // Whether a type does arithmetic with its parameter: its init.php asks for a number through
  // inits/number.php.

  function pqNumberType ( $seq ) {

    static $numeric = [];

    return $numeric [$seq] ??= ( padFileExists ( PT . "$seq/init.php" )
                                 and str_contains ( file_get_contents ( PT . "$seq/init.php" ), "include PQ . 'inits/number.php'" ) );

  }

  // Whether a type steps its walk itself: its init.php sets the increment - even and odd
  // double it, multiple and step take their parameter - so the values its loop walks are not
  // its positions.

  function pqStepType ( $seq ) {

    static $steps = [];

    return $steps [$seq] ??= ( padFileExists ( PT . "$seq/init.php" )
                               and preg_match ( '/\$pqInc\s*=[^=]/', file_get_contents ( PT . "$seq/init.php" ) ) );

  }

  function pqParmTerm ( $seq, $parm ) {

    if ( $parm === TRUE or is_numeric ( $parm ) or ! pqNumberType ( $seq ) )
      return TRUE;

    padError ( "The $seq sequence needs a number as its parameter, not '" . ( is_scalar ( $parm ) ? $parm : 'a list' ) . "'" );

    return FALSE;

  }

  // The term at position $n of a type built by position - a play asks an order type for it -
  // from the terms made for the request under pqTermsKey (), extended by pqTermsCount () when
  // $n is past them; FALSE when the type has no term there. A type whose terms can come
  // out otherwise another time is made afresh each time, as before.
  //
  // The terms are asked by count, with the ceiling of candidates, as pqPosition () asks them:
  // asked to= the position, a type that tests or steps through values - a play with
  // build='order' - made the terms up to the value n, and {sequence '1..6', make, multiple=3,
  // build='order'} answered nothing, step=3 only 1. A walk that came up short is all there
  // is, and is not made again for a later position.

  function pqNth ( $seq, $parm, $n ) {

    $key  = pqTermsKey ( $seq, 'nth', $parm );
    $made = pqTermsMade ( $key );

    if ( count ( $made ['terms'] ) < $n and $made ['end'] === '' ) {
      $made = pqTermsCount ( $seq, $parm, $n, $made );
      if ( $key !== '' )
        pqTermsMade ( $key, $made );
    }

    return $made ['terms'] [$n - 1] ?? FALSE;

  }

  // A list as the parameter of a type: only the list type takes one, as its values. Any other
  // type is named under the error, and carries on as the bare type - chance=[2, 3], random=,
  // eval=, range= ended the request on a str_contains, preg_match or explode TypeError, and
  // and=[2, 3] or oeis=[2, 3] read the list as the number 1 in silence.

  function pqParmList ( $seq, &$parm ) {

    if ( ! is_array ( $parm ) or $seq == 'list' )
      return;

    padError ( "The $seq sequence needs " . ( pqNumberType ( $seq ) ? 'a number' : 'a single value' )
             . " as its parameter, not a list" );

    $parm = TRUE;

  }

  // A list an action is given as its parameter - from an expression, sequence:merge([1,2],
  // [4]), or as a tag's option, intersection=b - is kept as a store under an __action__ name
  // for the run, and that name is what the action reads. Only an action that takes stores
  // (actions/double/, actions/merge/) takes one, and only a list of values: any other action
  // was handed the random name and said "first= takes a count, not '__action__xV1nsEup'", a
  // message of another text every request, and a list of lists ended the request on "Array to
  // string conversion". NULL, after the error, when the list is no parameter of the action.

  // Whether a value piped into an action is something to act on: a list, or the name of a
  // store.

  function pqActionPiped ( $value ) {

    return is_array ( $value ) or ( is_string ( $value ) and isset ( $GLOBALS ['pqStore'] [$value] ) );

  }

  function pqActionList ( $action, $list ) {

    foreach ( $list as $one )
      if ( ! is_scalar ( $one ) ) {
        padError ( "$action= takes a list of values, not a list of lists" );
        return NULL;
      }

    if ( ! padFileExists ( PQ . "actions/double/$action" ) and ! padFileExists ( PQ . "actions/merge/$action" ) ) {
      padError ( "$action= takes no list" );
      return NULL;
    }

    $name = '__action__' . padRandomString ();

    $GLOBALS ['pqStore'] [$name] = array_values ( $list );

    return $name;

  }

  function pqAction ( $action  ) {

    if ( is_string ( $action ) and isset ( pqNames ( PA ) ["$action.php"] ) )
      return TRUE;
    else
      return FALSE;

  }

  // Whether a tag option is one the subsystem reads: a name with a file in options/types/.
  // The files of store, single and operation only keep the names from a store (exits/store/
  // check.php): bare, they are words of the spelling a store's tag is written in - {mySequence
  // store} in apps/sequence/check/store.pad - but nothing reads a value given to one, and
  // excused as an option {sequence '1..5', store='nums'} kept no store while the strict check
  // let it pass, where stor='nums' is named.

  function pqOption ( $option, $value = TRUE ) {

    if ( ! is_string ( $option ) or ! isset ( pqNames ( PQ . 'options/types' ) ["$option.php"] ) )
      return FALSE;

    return ( $value === TRUE or ! in_array ( $option, [ 'store', 'single', 'operation' ] ) );

  }

  function pqDone ( $option, &$array ) {

    $key = array_search ( $option, $array );

    if ( $key === FALSE )
      return FALSE;

    unset ( $array [$key] );

    return TRUE;

  }

  function pqStore ( $check ) {

    return in_array ( $check, ['pull','fixed','build','given'] );

  }

  function pqPlay ( $check ) {

    return in_array ( $check, ['make','keep','remove','flag'] );

  }

  function pqRandom ( $array, $count=1, $order=0, $dups=0, $once=0 ) {

    if  ( ! is_array ( $array ) or ! count ( $array ) )
      return [];

    // Only no count at all draws every member. A count of 0 draws none, as first=0 and
    // last=0 take none - it was read as no count, so random=0 and randomize=0 drew them all.
    // Under atLeastOnce the count gives way to every member, a count of 0 as any other.

    if ( $count === TRUE or $count === NULL or $count === '' )
      $count = count ( $array );

    if ( (int) $count < 1 and ! $once )
      return [];

    // Without duplicates a member is drawn once at most, so a count past the members draws
    // them all: it went over to the draw with repeats, and {sequence '1..5', randomize=6}
    // answered 3 3 4 4 2 1 - two members twice and 5 not at all.

    if ( ! $dups and ! $once and $count > count ( $array ) )
      $count = count ( $array );

    if ( $dups or $once )
      return pqRandomDups ( $array, $count, $order, $once );
    else
      return pqRandomKeys ( $array, $count, $order );

  }

  function pqRandomKeys ( $array, $count, $order ) {

    if ( $count == 1 )
      $keys = [ 0 => array_rand ( $array ) ];
    else
      $keys = array_rand ( $array, $count );

    if ( ! $order  )
      shuffle ( $keys );

    foreach ( $keys as $k )
      $out [$k] = $array [$k];

    return $out;

  }

  function pqRandomDups ( $array, $count, $order, $once ) {

    if ( $once ) {
      $keys = array_keys ( $array );
      $count = $count - count ( $array );
    }

    for ( $i=1; $i <= $count; $i++ )
      $keys [] = array_rand ( $array ) ;

    if ( ! $order )

      shuffle ( $keys );

    else {

      $dups = array_count_values ( $keys );
      $keys = [];

      foreach ( $array as $k => $v )
        if ( isset ( $dups [$k] ) )
          for ($i=0; $i < $dups [$k]; $i++)
            $keys [] = $k;

    }

    foreach ( $keys as $k )
      if ( isset ( $out [$k] ) )
        $out [] = $array [$k];
      else
        $out [$k] = $array [$k];

    return $out;

  }

  function pqCorrectParms (&$pqPrm1, &$pqPrm2, &$pqPrm3) {

    if ( str_contains ( $pqPrm1, '|' ) and ! $pqPrm2 ) {

      $padTmp = padExplode ( $pqPrm1, '|', 2 );

      $pqPrm1 = $padTmp [0];
      $pqPrm2 = $padTmp [1];

    }

    if ( str_contains ( $pqPrm1, '|' ) and ! $pqPrm3 ) {

      $padTmp = padExplode ( $pqPrm1, '|', 2 );

      $pqPrm1 = $padTmp [0];
      $pqPrm3 = $padTmp [1];

    }

    if ( str_contains ( $pqPrm2, '|' ) and ! $pqPrm3 ) {
      $padTmp = padExplode ( $pqPrm2, '|', 2 );
      $pqPrm2 = $padTmp [0];
      $pqPrm3 = $padTmp [1];

    }

  }

  function pqRandomLy ( $for, $start, $end, $inc ) {

    if ( is_array ( $for ) and count ( $for ) )
      return $for [array_rand($for)];

    $loop = rand ( $start, $end );

    if ( $inc != 1 ) {
      $done = $loop - $start;
      $loop = $start + round ( $done / $inc ) * $inc;
      if ( $loop > $end )
        $loop = $end;
    }

    return $loop;

  }

  // Whether $x is a position an arithmetic sequence has: a whole number from 1 up. The
  // membership predicates of add, subtract, multiply and divide reduce to it - a value is a
  // term when the position it would stand at is one.

  function pqBoolPosition ( $x ) {

    if ( ! is_numeric ( $x ) )
      return FALSE;

    $x = $x + 0;

    return $x >= 1 and abs ( $x - round ( $x ) ) < 1e-9;

  }

  // Whether a parameter is a number past the integer range - a stored 1e20 handed to and=,
  // or= or xor= - which a bitwise type cannot cast: (int) ended the request on "The float
  // 1.0E+20 is not representable as an int". Such a parameter makes no term.

  function pqPastInt ( $value ) {

    if ( ! is_numeric ( $value ) )
      return FALSE;

    $value = $value + 0;

    return is_float ( $value ) and ( is_nan ( $value ) or $value < PHP_INT_MIN or $value >= PHP_INT_MAX );

  }

  // The key a value is told apart by, for a set of the values seen: a number by the number it
  // spells - 1, '1', '1.0' and 1.0 are one value, as in_array () takes them - a fraction as it
  // prints, anything else by its text. unique and merge looked every value up in all the
  // values kept so far, and the run took time with the square of its length. One key for
  // unique, merge, dedup, distinct and the membership checks: merge told a computed
  // 0.30000000000000004 from a listed 0.3 by all 17 digits and printed 0.3 0.3, where dedup
  // took the two, which print alike, for one value.

  //
  // A whole number written past the integer range is the digits it is written with - its
  // sign, no leading zeros: as a float 9223372036854775808 and 9223372036854775809 were one
  // value, unique kept the first alone and an intersection with a store of the one let the
  // other through.

  function pqValueKey ( $value ) {

    if ( ! is_numeric ( $value ) )
      return 's' . $value;

    if ( is_string ( $value ) and preg_match ( '/^\s*([+-]?)0*(\d+)\s*$/', $value, $pqWhole ) and ! is_int ( $value + 0 ) )
      return ( ( $pqWhole [1] == '-' ) ? '-' : '' ) . $pqWhole [2];

    $value = $value + 0;

    if ( is_float ( $value ) and floor ( $value ) == $value and $value >= PHP_INT_MIN and $value < PHP_INT_MAX )
      $value = (int) $value;

    return (string) $value;

  }

  // The key of a value in an index of pqIndex (): an integer as it is - the terms of the
  // tables and of most lists are integers - anything else as pqValueKey () spells it, which
  // spells an integer as the same key.

  function pqIndexKey ( $value ) {

    return is_int ( $value ) ? $value : pqValueKey ( $value );

  }

  // An index of a list of terms: the key of each value, pqIndexKey (), with the position it
  // first stands at, from 1. Asked with isset () it answers what in_array () answered over
  // the list, without walking it.

  function pqIndex ( $list ) {

    $index = [];

    foreach ( array_values ( $list ) as $at => $value )
      $index [ pqIndexKey ( $value ) ] ??= $at + 1;

    return $index;

  }

  // How a type answers the membership question of build/check.php - 'bool', 'fixed',
  // 'build', 'table' or 'terms' - settled once a request: the files of a type do not change
  // under it. Its generated.php is loaded here, so the answer does not depend on whether a
  // build loaded it first.

  function pqCheckKind ( $seq ) {

    static $kinds = [];

    if ( isset ( $kinds [$seq] ) )
      return $kinds [$seq];

    if ( padFileExists ( PT . "$seq/generated.php" ) )
      include_once PT . "$seq/generated.php";

    if     ( padFileExists ( PT . "$seq/bool.php" )                           ) $kind = 'bool';
    elseif ( padFileExists ( PT . "$seq/fixed.php" )                          ) $kind = 'fixed';
    elseif ( padFileExists ( PT . "$seq/build.php" )                          ) $kind = 'build';
    elseif ( defined ( "PAD$seq" ) and count ( constant ( "PAD$seq" ) )       ) $kind = 'table';
    else                                                                        $kind = 'terms';

    return $kinds [$seq] = $kind;

  }

  // A parameter as part of a key: a number or a text by its text, a list by its serialized
  // form, and the bare option's TRUE - FALSE and NULL too - by a mark of its own, each kind
  // behind a letter so no text can spell another kind's key. TRUE was its text, '1', the key
  // of the parameter 1, where range and repeat read the bare TRUE otherwise: after
  // sequence:repeat(2) sequence:repeat(2, 1) answered 2 where it is 1, and keep, range=1 after
  // keep, range kept 1 2 3 4 5 where it keeps 1.

  function pqCheckParm ( $parm ) {

    if     ( $parm === TRUE  ) return 'T';
    elseif ( $parm === FALSE ) return 'F';
    elseif ( $parm === NULL  ) return 'N';
    elseif ( is_scalar ( $parm ) ) return 's' . $parm;
    else                           return 'a' . serialize ( $parm );

  }

  // The key of the list a type returns whole - its fixed.php or build.php - made from its
  // parameter, the increment and the tag's own first parameter, as build/check.php and the
  // plays keep it. Kept for the last ones asked, which are compared by identity, at once for
  // the same list: made for every candidate, the list of list=pull:b was serialized into the
  // key each time, and a keep over its 10,000 values took 1.7 seconds.

  function pqListKey ( $kind, $seq, $parm, $inc, $first ) {

    static $last = [];

    $for = [ $kind, $seq, $parm, $inc, $first ];

    foreach ( $last as [ $was, $key ] )
      if ( $was === $for )
        return $key;

    $key = "$kind|$seq|" . pqCheckParm ( $parm ) . '|' . pqCheckParm ( $inc ) . '|' . pqCheckParm ( $first );

    $last [] = [ $for, $key ];

    if ( count ( $last ) > 8 )
      array_shift ( $last );

    return $key;

  }

  // The terms of a type as far as position $to, for build/check.php to look in: their index,
  // pqIndex (), the first term, and what pqCheckReach () reads of the numbers among them - the
  // lowest, the highest, the one halfway and the last, and whether they never go down or never
  // go up, every term a number.

  function pqTermsIndex ( $seq, $parm, $to, $until = NULL ) {

    return pqTermsIndexOf ( pqTerms ( $seq, $parm, [ 'to' => $to ], $until ), $to );

  }

  function pqTermsIndexOf ( $terms, $to, $index = NULL ) {

    $numbers = array_values ( array_filter ( $terms, 'is_numeric' ) );
    $count   = count ( $numbers );
    $rising  = $falling = ( $count == count ( $terms ) );

    for ( $i = 1; ( $rising or $falling ) and $i < $count; $i++ ) {
      if ( $numbers [$i] < $numbers [$i - 1] ) $rising  = FALSE;
      if ( $numbers [$i] > $numbers [$i - 1] ) $falling = FALSE;
    }

    return [
      'to'      => $to,
      'index'   => $index ?? pqIndex ( $terms ),
      'first'   => array_slice ( $terms, 0, 1 ),
      'low'     => $count ? min ( $numbers ) : NULL,
      'high'    => $count ? max ( $numbers ) : NULL,
      'half'    => $count ? [ intdiv ( $count, 2 ), $numbers [ max ( 0, intdiv ( $count, 2 ) - 1 ) ] ] : NULL,
      'last'    => $count ? [ $count, $numbers [$count - 1] ] : NULL,
      'rising'  => $rising,
      'falling' => $falling
    ];

  }

  // How far terms made as far as they are - pqTermsIndex () - are made on to look for $value,
  // within position $to, or 0 when it is not looked for further: a value between the numbers
  // made may stand at any later position, and is looked for as far as $to, as before - unless
  // the terms never go down, or never go up, when it would stand among them already; one past
  // all of them only while the terms head for it, as far as the position they reach it at when
  // they grow as they grow over the second half of what was made - as a power of the position,
  // so a square or a fourth power is seen to come there. A value past terms that are no
  // numbers, that stand still or turn back, or that climb too slowly - '@ * 2' gets to
  // 9223372036854775807 at position 4.6 * 10^18 - is not looked for on.

  function pqCheckReach ( $made, $value, $to ) {

    if ( ! is_numeric ( $value ) or $made ['last'] === NULL )
      return 0;

    if ( $value >= $made ['low'] and $value <= $made ['high'] )
      return ( $made ['rising'] or $made ['falling'] ) ? 0 : $to;

    $sign = ( $value > $made ['high'] ) ? 1 : -1;

    [ $at,  $half ] = $made ['half'];
    [ $end, $last ] = $made ['last'];

    $half  = $sign * $half;
    $last  = $sign * $last;
    $value = $sign * $value;

    if ( $last <= $half or $at < 1 or $end <= $at )
      return 0;

    if ( $half <= 0 )
      return $to;

    $reach = $end * ( $value / $last ) ** ( log ( $end / $at ) / log ( $last / $half ) );

    return ( $reach <= $to ) ? (int) ceil ( $reach ) : 0;

  }

  // The terms of a type at twice, four times ... position $made and at position $most - one
  // at a time, each a run of its own from that position - for build/check.php to see where the
  // terms of an expression go past those it made.

  function pqCheckSamples ( $seq, $parm, $made, $most ) {

    $samples = [];

    for ( $at = 2 * max ( 1, $made ); $at < $most; $at *= 2 )
      $samples = array_merge ( $samples, pqTerms ( $seq, $parm, [ 'from' => $at, 'to' => $at ] ) );

    return array_merge ( $samples, pqTerms ( $seq, $parm, [ 'from' => $most, 'to' => $most ] ) );

  }

  // What the samples pqCheckSamples () made say of $value, a number past every term made: TRUE
  // when one of them is the value, FALSE when every one stays short of it as the terms made do
  // - a number on their side of it, or no number when they were none - and NULL when they say
  // neither.
  //
  // A sample is the value when their keys name one entry of an index, as the index of the terms
  // made is asked: compared as strings, since pqIndexKey () gives an int as it is and anything
  // else as its key text. Compared as they came, the term "2000000" an expression's ternary made
  // was never the candidate 2000000, nor 5 the "5" of '@ le 20000 ? 0 : 5', and each was looked
  // for in a million terms made one by one - two and a half seconds - where the sample had it.

  function pqCheckSeen ( $made, $value, $at ) {

    $short = TRUE;

    foreach ( $made ['samples'] as $sample ) {

      if ( (string) pqIndexKey ( $sample ) === (string) $at )
        return TRUE;

      if ( $made ['last'] === NULL )
        $short = $short && ! is_numeric ( $sample );
      else
        $short = $short && is_numeric ( $sample ) && ( ( $value > $made ['high'] ) ? $sample < $value : $sample > $value );

    }

    return $short ? FALSE : NULL;

  }

  // The terms of an expression to the ceiling of candidates, $most, for build/check.php to look
  // value key $at up in when the samples said nothing of it - made a stretch at a time, each
  // twice as far as the one before, and looked in as they come, so a value made early is found
  // without the rest of the million being made. The walk is kept in $made, the terms made
  // as far as the reach, and goes on from where it stopped for the next value not found yet;
  // at the ceiling $made becomes what the terms there make of it, pqTermsIndexOf () with the
  // index the walk made - pqIndex () of them - as the one run to the ceiling made it. Made in one run every time, {sequence '150;500;1000;4000', keep,
  // eval='@ + 1'} made a million terms for 150, made at position 149, and took two seconds.
  //
  // A stretch that comes back short - a position that made no term, or one past the integer
  // range, which ends a run - is left for one run to the ceiling, as before: run whole, the
  // terms end at the first one out of range, and stretches would go on past it.
  //
  // The walk has the time the one run had, $padSeqMaxSeconds, for all its stretches together:
  // each is given what is left of it ('spent' counts what they took, so a walk gone on with
  // for a later value, after other work, is not charged for that work), and the one run a
  // short stretch leaves is given the rest. A stretch the clock cut ends the walk at the
  // ceiling with the terms made, as the clock ended the one run with the terms it made. Each
  // stretch had five seconds of its own, and one cut by the clock came back short and started
  // the one run - with five more: keep of 50001 over an expression of 10 us a term took 10.6
  // seconds where the one run took 5.3, and under Xdebug 18 where it took 6.

  function pqCheckWalk ( &$made, $seq, $parm, $at, $most ) {

    $made ['walk'] ??= [ 'to' => 0, 'terms' => [], 'index' => [], 'spent' => 0 ];

    $seconds = $GLOBALS ['padSeqMaxSeconds'] ?? 5;
    $budget  = ( is_numeric ( $seconds ) and is_finite ( (float) $seconds ) and $seconds > 0 ) ? $seconds * 1e9 : INF;

    while ( ! isset ( $made ['walk'] ['index'] [$at] ) and $made ['walk'] ['to'] < $most ) {

      $from  = $made ['walk'] ['to'] + 1;
      $to    = min ( $most, 2 * max ( $made ['walk'] ['to'], $made ['to'] ) );
      $start = hrtime ( TRUE );
      $until = $start + $budget - $made ['walk'] ['spent'];
      $terms = pqTerms ( $seq, $parm, [ 'from' => $from, 'to' => $to ], $until );
      $out   = ( hrtime ( TRUE ) > $until );

      if ( count ( $terms ) != $to - $from + 1 and ! $out ) {
        $made = pqTermsIndex ( $seq, $parm, $most, $until );
        return isset ( $made ['index'] [$at] );
      }

      foreach ( $terms as $term ) {
        $made ['walk'] ['terms'] [] = $term;
        $made ['walk'] ['index'] [ pqIndexKey ( $term ) ] ??= count ( $made ['walk'] ['terms'] );
      }

      $made ['walk'] ['spent'] += hrtime ( TRUE ) - $start;
      $made ['walk'] ['to']     = $out ? $most : $to;

    }

    $found = isset ( $made ['walk'] ['index'] [$at] );

    if ( $made ['walk'] ['to'] >= $most )
      $made = pqTermsIndexOf ( $made ['walk'] ['terms'], $most, $made ['walk'] ['index'] );

    return $found;

  }

  // The key the terms of an eval= expression are kept under for the request, or '' when they
  // are made for the run: an expression of the value, numbers, strings and operators alone
  // gives the same terms every time. One with a field gives others for another value of it,
  // and a function - which may draw at random - a property or an option of the tag may answer
  // otherwise another time.

  function pqEvalKey ( $eval ) {

    if ( ! is_string ( $eval ) or ( $tokens = padEvalTokens ( $eval ) ) === FALSE )
      return '';

    foreach ( $tokens as $token )
      if ( $token [1] == 'other' ) {
        if ( ! in_array ( strtoupper ( $token [0] ), padEval_txt ) and ! in_array ( strtolower ( $token [0] ), padEval_tests ) )
          return '';
      } elseif ( ! in_array ( $token [1], [ 'VAL', 'OPR', '$$', 'open', 'close', 'a-open', 'a-close', 'pipe', 'arrow', 'hex', '%' ] ) )
        return '';

    return "eval|$eval";

  }

  // A float -0 made 0, any other number as it is - what x + 0 did for the arithmetic of a
  // sequence type, until PHP's optimizer, sure of a float, took x + 0 for x and dropped it:
  // under OPcache without Xdebug {sequence modulo=3, from=-3} printed -0 again.

  function pqZero ( $x ) {

    return ( is_float ( $x ) and $x == 0 ) ? 0.0 : $x;

  }

  // Whether $x is a whole number an integer predicate can work with: a number, without a
  // fraction, inside the integer range. The membership tests of the integer sequences -
  // prime, even, happy and the rest - answer no for anything else before doing arithmetic on
  // it: keep, remove and flag hand them whatever the list holds, and a fraction or a word
  // ended the request inside a % or an &, or in gmp_prob_prime().

  function pqBoolWhole ( $x ) {

    if ( ! is_numeric ( $x ) )
      return FALSE;

    $x = $x + 0;

    if ( is_int ( $x ) )
      return TRUE;

    return is_finite ( $x ) and floor ( $x ) == $x and $x >= PHP_INT_MIN and $x < PHP_INT_MAX;

  }

  // The product of the factors divided by a divisor that divides it exactly - the figurate
  // formulas n(3n - 1)/2, n(n + 1)(n + 2)/6 and the like - with the divisor taken out of the
  // factors before they are multiplied, so nothing formed along the way is larger than the
  // term itself. Multiplied out first, 3n^2 passed PHP_INT_MAX while (3n^2 - n)/2 still fit,
  // and the term came back as an imprecise float. Factors that are not all whole numbers
  // are multiplied out as before.

  function pqProduct ( $factors, $divisor ) {

    // PHP_INT_MIN too, whose absolute value is no integer.

    foreach ( $factors as $factor )
      if ( ! pqBoolWhole ( $factor ) or $factor == PHP_INT_MIN )
        return array_product ( $factors ) / $divisor;

    foreach ( $factors as $key => $factor ) {

      $gcd  = abs ( (int) $factor );
      $rest = $divisor;

      while ( $rest ) {
        $swap = $gcd % $rest;
        $gcd  = $rest;
        $rest = $swap;
      }

      $factors [$key] = intdiv ( (int) $factor, $gcd );
      $divisor        = intdiv ( $divisor, $gcd );

    }

    return array_product ( $factors ) / $divisor;

  }

  // $x rounded up to a multiple of $p, as ceil ( $x / $p ) * $p rounds it, but in whole numbers
  // when both are whole and $p is above 0: through a float the quotient lost its last digits
  // past 2^53, and {sequence multiple=3, from=-9223372036854775808} began on the imprecise
  // -9.2233720368548E+18 rather than on -9223372036854775806. A bare multiple holds TRUE, the
  // step of 1 it is read as: refused as no whole number, it went through the float too, and
  // {sequence multiple, from=9223372036854775800, rows=3} answered nothing.

  function pqCeilMultiple ( $x, $p ) {

    if ( $p === TRUE )
      $p = 1;

    if ( ! pqBoolWhole ( $x ) or ! pqBoolWhole ( $p ) or $p <= 0 )
      return ceil ( $x / $p ) * $p;

    $x    = (int) $x;
    $p    = (int) $p;
    $rest = $x % $p;

    if     ( $rest == 0 ) return $x;
    elseif ( $rest  > 0 ) return $x + ( $p - $rest );
    else                  return $x - $rest;

  }

  // $x rounded down, and to the nearest - half away from 0, as round () rounds - to a multiple
  // of $p, in whole numbers when both are whole and $p is above 0, else through floats as
  // floor ( $x / $p ) * $p and round ( $x / $p ) * $p: the float quotient lost the last digits
  // past 2^53, and from 15 digits the terms of floor, round and ceil printed in E-notation.
  // A bare parameter holds TRUE, the 1 it is read as.
  //
  // The floor takes the remainder off, and a whole $p more when the remainder is below 0, so
  // no sum passes the integer range on the way: ( $x % $p ) + $p did once the remainder and
  // the parameter together passed PHP_INT_MAX, and the % of that float ended the request -
  // {sequence floor=9223372036854775807} on "The float 9.223372036854776E+18 is not
  // representable as an int". Only a floor below PHP_INT_MIN leaves the range now, as the
  // float that ends the run.

  function pqFloorMultiple ( $x, $p ) {

    if ( $p === TRUE )
      $p = 1;

    if ( ! pqBoolWhole ( $x ) or ! pqBoolWhole ( $p ) or $p <= 0 )
      return floor ( $x / $p ) * $p;

    $x    = (int) $x;
    $p    = (int) $p;
    $rest = $x % $p;

    return $rest < 0 ? $x - $rest - $p : $x - $rest;

  }

  function pqRoundMultiple ( $x, $p ) {

    if ( $p === TRUE )
      $p = 1;

    if ( ! pqBoolWhole ( $x ) or ! pqBoolWhole ( $p ) or $p <= 0 )
      return round ( $x / $p ) * $p;

    $x    = (int) $x;
    $p    = (int) $p;
    $rest = $x % $p;

    if ( 2 * abs ( $rest ) < $p )
      return $x - $rest;

    return $x - $rest + ( $x < 0 ? - $p : $p );

  }

  // Whether $x is a term of the figurate sequence n(an - b)/d, n from 0 up: n is about the
  // square root of dx/a, so the root is taken once and the whole n next to it are tried
  // with the exact pqProduct the type generates with. Generated up to the candidate
  // instead, the membership check stopped at the million-candidate ceiling and answered no
  // past it - 10^16 was no square. 0 is the term at position 0, which the types make: it was
  // turned down, and {sequence pentagonal, from=0} made a 0 its own test denied.

  function pqBoolFigurate ( $x, $a, $b, $d ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    if ( $x == 0 )
      return TRUE;

    $x = (int) $x;
    $n = (int) sqrt ( $d * (float) $x / $a );

    for ( $i = max ( 1, $n - 1 ); $i <= $n + 2; $i++ )
      if ( pqProduct ( [ $i, $a * $i - $b ], $d ) == $x )
        return TRUE;

    return FALSE;

  }

  // The build a type runs when none is named: its own file of the build asked for, else the
  // first of these it has. The order makes a type's way of producing its terms its default -
  // a loop, a recurrence, a formula of the position, a list - before a bool.php predicate, so
  // a type that is generated some other way can also have a membership test, as golomb and
  // kolakoski do, without its terms being made by walking the values instead: were bool.php
  // placed before the recurrence, golomb's would make {sequence golomb} the run of the numbers
  // that belong to it, 1 2 3 4 for 1 2 2 3.

  function pqBuild ( $check, $for='' ) {

    if ( $check == 'pull' )
      return 'fixed';

    if ( $for == 'keep' or $for == 'remove' or $for == 'flag' )
      return 'check';

    if ( padFileExists ( PT . "$check/$for.php" ) )
      return $for;

    if     ( padFileExists ( PT . "$check/loop.php")      ) return 'loop';
    elseif ( padFileExists ( PT . "$check/make.php")      ) return 'make';
    elseif ( padFileExists ( PT . "$check/order.php")     ) return 'order';
    elseif ( padFileExists ( PT . "$check/function.php")  ) return 'function';
    elseif ( padFileExists ( PT . "$check/build.php")     ) return 'build';
    elseif ( padFileExists ( PT . "$check/fixed.php")     ) return 'fixed';
    elseif ( padFileExists ( PT . "$check/bool.php")      ) return 'bool';
    elseif ( padFileExists ( PT . "$check/generated.php") ) return 'generated';
    else                                                    return 'unknown';

  }

  // The digits of a whole number in reverse order. They are taken off with intdiv (): the
  // float division by 10 lost the last digits past 2^53, and 99999999999999999 was no
  // palindrome. A reversal past the integer range is the float it overflows to, which no whole
  // number equals.

  function padTypeReverse ( $x ) {

    if ( pqBoolWhole ( $x ) )
      $x = (int) $x;

    $rev = 0;

    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x   = is_int ( $x ) ? intdiv ( $x, 10 ) : (int) ( $x / 10 );
    }

    return $rev;

  }

  function pqTruncate ( $array, $side, $count ) {

    if ( $count < 1 )
      return $array;

    $count = (int) $count;

    if ( $side == 'left' )
      return array_slice ( $array, $count );
    else
      return array_slice ( $array, 0, $count * -1 );

  }

?>

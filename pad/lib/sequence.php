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
  // pqArray goes the other way round: it renders {sequence ...} through padCode and hands
  // the result back to PHP as an array.

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

  function pqRandomBetween ( $from, $to ) {

    $from = (int) $from;
    $to   = (int) $to;

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

  function pqArray ( $sequence, $parm='', $options='') {

    // Only TRUE or nothing at all means no parameter - a 0 is a value like any other, and
    // the truthiness test glued it onto the name (add=0 asked for a sequence named add0).

    if ( $parm === TRUE or $parm === FALSE or $parm === NULL or $parm === '' )
      $parm = '';
    else
      $parm = "=$parm";

    if ( $options )
      $options = ", $options";

    // Every term is rendered with a comma behind it, the last one too, so that one comma is
    // cut before the split - it used to stay and leave an empty last element, and an empty
    // sequence came back as [''] rather than [].

    $list = padCode ( "{sequence $sequence$parm$options}{\$sequence},{/sequence}" );

    if ( str_ends_with ( $list, ',' ) )
      $list = substr ( $list, 0, -1 );

    return ( $list === '' ) ? [] : explode ( ',', $list );

  }

  // pqTerms is pqArray for a sequence type the engine itself generates to look in: the type
  // and its parameter go to sequence/terms.php as data, the run is bounded by $parms - stop,
  // to or sole - and the terms come back as they were made, nothing rendered. A parameter of
  // nothing is the bare type, as pqArray writes it.

  function pqTerms ( $sequence, $parm, $parms ) {

    $pqSetAction = $sequence;
    $pqSetParm   = ( $parm === FALSE or $parm === NULL or $parm === '' ) ? TRUE : $parm;
    $pqSetParms  = $parms;

    return include PQ . 'sequence/terms.php';

  }

  // The value at position $n of a type whose terms are the values its bool.php test accepts -
  // what build='function' gives such a type, the position build. The values are made by the
  // type's own build, walked from 1, and kept for the request per type and parameter; asked
  // past what was made, the walk is made again for twice as many - from the position asked
  // for, so a walk to the ceiling is made only for a position past the last term there is.
  // A position the walk cannot
  // reach in the candidates and the time a run has answers a float past the integer range,
  // which ends the run as an overflowed term does - strong has four terms, and asked for a
  // fifth it would walk to the ceiling for every position after it.

  function pqPosition ( $seq, $parm, $n ) {

    static $made = [];

    if ( $n < 1 )
      return FALSE;

    $key = $seq . '|' . pqCheckParm ( $parm );

    if ( ! isset ( $made [$key] ) or ( count ( $made [$key] [1] ) < $n and ! $made [$key] [0] ) ) {
      $want         = max ( $n, 2 * count ( $made [$key] [1] ?? [] ) );
      $terms        = pqTerms ( $seq, $parm, [ 'rows' => $want, 'try' => $GLOBALS ['padSeqMaxTries'] ?? 1000000 ] );
      $made [$key]  = [ count ( $terms ) < $want, $terms ];
    }

    return $made [$key] [1] [$n - 1] ?? (float) PHP_INT_MAX * 2;

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

  // The terms kept under a key of pqTermsKey (), or with $terms given, keeps them when they
  // are more than were kept.

  function pqTermsMade ( $key, $terms = NULL ) {

    static $made = [];

    if ( $terms !== NULL and count ( $terms ) > count ( $made [$key] ?? [] ) )
      $made [$key] = $terms;

    return $made [$key] ?? [];

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

  function pqParmTerm ( $seq, $parm ) {

    static $numeric = [];

    if ( $parm === TRUE or is_numeric ( $parm ) )
      return TRUE;

    $numeric [$seq] ??= ( padFileExists ( PT . "$seq/init.php" )
                          and str_contains ( file_get_contents ( PT . "$seq/init.php" ), "include PQ . 'inits/number.php'" ) );

    if ( ! $numeric [$seq] )
      return TRUE;

    padError ( "The $seq sequence needs a number as its parameter, not '" . ( is_scalar ( $parm ) ? $parm : 'a list' ) . "'" );

    return FALSE;

  }

  // The term at position $n of a type built by position - a play asks an order type for it -
  // from the terms made for the request under pqTermsKey (), made again for twice as many
  // when $n is past them; FALSE when the type has no term there. A type whose terms can come
  // out otherwise another time is made afresh each time, as before.

  function pqNth ( $seq, $parm, $n ) {

    $key  = pqTermsKey ( $seq, 'nth', $parm );
    $made = ( $key !== '' ) ? pqTermsMade ( $key ) : [];

    if ( count ( $made ) < $n ) {
      $made = pqTerms ( $seq, $parm, [ 'to' => max ( $n, 2 * count ( $made ) ) ] );
      if ( $key !== '' )
        pqTermsMade ( $key, $made );
    }

    return $made [$n - 1] ?? FALSE;

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

    if ( ! padFileExists ( PQ . "options/types/$option.php" ) )
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
  // spells - 1, '1', '1.0' and 1.0 are one value, as in_array () takes them - anything else
  // by its text. unique and merge looked every value up in all the values kept so far, and
  // the run took time with the square of its length.

  function pqValueKey ( $value ) {

    if ( ! is_numeric ( $value ) )
      return 's' . $value;

    $value = $value + 0;

    if ( is_float ( $value ) and floor ( $value ) == $value and $value >= PHP_INT_MIN and $value < PHP_INT_MAX )
      $value = (int) $value;

    return is_float ( $value ) ? sprintf ( '%.17g', $value ) : (string) $value;

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

  // A parameter as part of a key: its text, or for a list its serialized form.

  function pqCheckParm ( $parm ) {

    return is_scalar ( $parm ) ? (string) $parm : serialize ( $parm );

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

  // Whether $x is a term of the figurate sequence n(an - b)/d, n from 1 up: n is about the
  // square root of dx/a, so the root is taken once and the whole n next to it are tried
  // with the exact pqProduct the type generates with. Generated up to the candidate
  // instead, the membership check stopped at the million-candidate ceiling and answered no
  // past it - 10^16 was no square.

  function pqBoolFigurate ( $x, $a, $b, $d ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

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

  function padTypeReverse ( $x ) {

   $rev = 0;

    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x = (int) ($x / 10);
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

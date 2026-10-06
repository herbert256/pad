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

  function pqShuffle ( &$array ) {

    $shuffled = [];
    $keys     = array_keys ( $array );

    shuffle ( $keys );

    foreach ( $keys as $key )
      $shuffled [$key] = $array [$key];

    $array = $shuffled;

  }

  function pqSeq ( $seq  ) {

    if ( $seq and file_exists ( PT . "$seq" ) )
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

  function pqAction ( $action  ) {

    if ( $action and file_exists ( PA . "$action.php" ) )
      return TRUE;
    else
      return FALSE;

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

    if ( $dups or $count > count ( $array ) or $once )
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

  function pqBuild ( $check, $for='' ) {

    if ( $check == 'pull' )
      return 'fixed';

    if ( $for == 'keep' or $for == 'remove' or $for == 'flag' )
      return 'check';

    if ( file_exists ( PT . "$check/$for.php" ) )
      return $for;

    if     ( file_exists ( PT . "$check/loop.php")      ) return 'loop';
    elseif ( file_exists ( PT . "$check/make.php")      ) return 'make';
    elseif ( file_exists ( PT . "$check/function.php")  ) return 'function';
    elseif ( file_exists ( PT . "$check/bool.php")      ) return 'bool';
    elseif ( file_exists ( PT . "$check/order.php")     ) return 'order';
    elseif ( file_exists ( PT . "$check/build.php")     ) return 'build';
    elseif ( file_exists ( PT . "$check/fixed.php")     ) return 'fixed';
    elseif ( file_exists ( PT . "$check/generated.php") ) return 'generated';
    else                                                  return 'unknown';

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

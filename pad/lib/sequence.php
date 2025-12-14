<?php

  /**
   * Extracts and normalizes first parameter for sequence actions.
   *
   * Shifts first element from params array and unwraps single-element
   * nested arrays.
   *
   * @param array &$parms The parameters array (modified by reference).
   *
   * @return mixed The normalized first parameter.
   */
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


  /**
   * Expands range syntax (from..to) to random value.
   *
   * If parameter contains '..' with numeric bounds, replaces
   * with random integer in that range.
   *
   * @param mixed &$parm The parameter to expand (modified in place).
   *
   * @return void
   */
  function pqRandomParm ( &$parm ) {

    if ( str_contains ( $parm, '..' ) ) {

      padSplit ( '..', $parm, $from, $to );

      if ( is_numeric ( $from ) and is_numeric ( $to ) )
        $parm = mt_rand ( $from, $to );

    }

  }


  /**
   * Expands triple-dot range syntax (from...to) to random value.
   *
   * Returns random integer if bounds are numeric, otherwise
   * returns original parameter.
   *
   * @param string $parm The parameter with potential range syntax.
   *
   * @return mixed Random integer or original parameter.
   */
  function pqRandomParm3 ( $parm ) {

    padSplit ( '...', $parm, $from, $to );

    if ( is_numeric ( $from ) and is_numeric ( $to ) )
      return mt_rand ( $from, $to );
    else
      return $parm;

  }


  /**
   * Shuffles array while preserving key-value associations.
   *
   * Unlike PHP's shuffle(), this maintains the key associations
   * while randomizing order.
   *
   * @param array &$array The array to shuffle (modified in place).
   *
   * @return void
   */
  function pqShuffle ( &$array ) {

    $shuffled = [];
    $keys     = array_keys ( $array );

    shuffle ( $keys );

    foreach ( $keys as $key )
      $shuffled [$key] = $array [$key];

    $array = $shuffled;

  }


  /**
   * Checks if a sequence type exists.
   *
   * @param string $seq The sequence name to check.
   *
   * @return bool TRUE if sequence exists, FALSE otherwise.
   */
  function pqSeq ( $seq  ) {

    if ( $seq and file_exists ( PT . "$seq" ) )
      return TRUE;
    else
      return FALSE;

  }



  /**
   * Generates array from sequence tag output.
   *
   * Processes sequence tag and splits comma-separated result
   * into array.
   *
   * @param string $sequence The sequence name.
   * @param mixed  $parm     Optional parameter value.
   * @param string $options  Optional tag options.
   *
   * @return array Array of sequence values.
   */
  function pqArray ( $sequence, $parm='', $options='') {

    if ( $parm)
      if ( $parm === TRUE )
        $parm = '';
      else
        $parm = ( $parm ) ? "=$parm" : '';

    if ( $options )
      $options = ", $options";

    return explode ( ',', padCode ( "{sequence $sequence$parm$options}{\$sequence},{/sequence}" ) );

  }


  /**
   * Checks if an action type exists.
   *
   * @param string $action The action name to check.
   *
   * @return bool TRUE if action exists, FALSE otherwise.
   */
  function pqAction ( $action  ) {

    if ( $action and file_exists ( PQ . "actions/types/$action.php" ) )
      return TRUE;
    else
      return FALSE;

  }


  /**
   * Removes an option from array if present.
   *
   * Searches for option value and removes it, returning whether
   * it was found.
   *
   * @param mixed $option The option value to remove.
   * @param array &$array The array to search (modified in place).
   *
   * @return bool TRUE if found and removed, FALSE otherwise.
   */
  function pqDone ( $option, &$array ) {

    $key = array_search ( $option, $array );

    if ( $key === FALSE )
      return FALSE;

    unset ( $array [$key] );

    return TRUE;

  }


  /**
   * Checks if value is a store type.
   *
   * @param string $check The value to check.
   *
   * @return bool TRUE if store type (pull/fixed/build/given).
   */
  function pqStore ( $check ) {

    return in_array ( $check, ['pull','fixed','build','given'] );

  }


  /**
   * Checks if value is a play type.
   *
   * @param string $check The value to check.
   *
   * @return bool TRUE if play type (make/keep/remove/flag).
   */
  function pqPlay ( $check ) {

    return in_array ( $check, ['make','keep','remove','flag'] );

  }


  /**
   * Selects random elements from array.
   *
   * Supports various modes: with/without duplicates, preserving
   * or shuffling order, ensuring each element appears at least once.
   *
   * @param array $array The source array.
   * @param int   $count Number of elements to select.
   * @param int   $order Preserve original order (1) or shuffle (0).
   * @param int   $dups  Allow duplicates (1) or not (0).
   * @param int   $once  Include each element at least once (1) or not (0).
   *
   * @return array Selected elements with original keys.
   */
  function pqRandom ( $array, $count=1, $order=0, $dups=0, $once=0 ) {

    if  ( ! is_array ( $array ) or ! count ( $array ) )
      return [];

    if ( ! $count or $count === TRUE )
      $count = count ( $array );

    if ( $dups or $count > count ( $array ) or $once )
      return pqRandomDups ( $array, $count, $order, $once );
    else
      return pqRandomKeys ( $array, $count, $order );

  }


  /**
   * Selects random keys without duplicates.
   *
   * @param array $array The source array.
   * @param int   $count Number of elements to select.
   * @param int   $order Preserve order (1) or shuffle (0).
   *
   * @return array Selected elements with original keys.
   */
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


  /**
   * Selects random keys allowing duplicates.
   *
   * Optionally ensures each element appears at least once before
   * adding random extras.
   *
   * @param array $array The source array.
   * @param int   $count Number of elements to select.
   * @param int   $order Preserve order (1) or shuffle (0).
   * @param int   $once  Include each element at least once.
   *
   * @return array Selected elements (may have duplicate values).
   */
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


  /**
   * Splits pipe-separated parameters into separate variables.
   *
   * If parm1 contains | and parm2/parm3 are empty, splits
   * the pipe-separated parts into those variables.
   *
   * @param string &$pqPrm1 First parameter (may contain pipes).
   * @param string &$pqPrm2 Second parameter.
   * @param string &$pqPrm3 Third parameter.
   *
   * @return void
   */
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


  /**
   * Generates random value from array or range with increment.
   *
   * If array provided, returns random element. Otherwise generates
   * random number in range, snapped to increment.
   *
   * @param mixed $for   Array of values or unused if using range.
   * @param int   $start Range start.
   * @param int   $end   Range end.
   * @param int   $inc   Increment to snap to.
   *
   * @return mixed Random element or number.
   */
  function pqRandomLy ( $for, $start, $end, $inc ) {

    if ( is_array ( $for ) and count ( $for ) )
      return $for [array_rand($for)];

    $loop = rand ( $start, $end );

    if ( $inc <> 1 ) {
      $done = $loop - $start;
      $loop = $start + round ( $done / $inc ) * $inc;
      if ( $loop > $end )
        $loop = $end;
    }

    return $loop;

  }


  /**
   * Determines build type for a sequence.
   *
   * Checks for specific type files in sequence directory and
   * returns the build mode to use.
   *
   * @param string $check The sequence name.
   * @param string $for   Optional specific build type to check.
   *
   * @return string Build type (loop, make, function, etc.).
   */
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


  /**
   * Reverses the digits of an integer.
   *
   * @param int $x The number to reverse.
   *
   * @return int The reversed number.
   */
  function padTypeReverse ( $x ) {

   $rev = 0;

    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x = (int) ($x / 10);
    }

    return $rev;

  }


  /**
   * Removes elements from left or right side of array.
   *
   * @param array  $array The array to truncate.
   * @param string $side  Which side: 'left' or 'right'.
   * @param int    $count Number of elements to remove.
   *
   * @return array The truncated array.
   */
  function pqTruncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ( $array, $count );
    else
      return array_slice ( $array, 0, $count * -1 );

  }


?>
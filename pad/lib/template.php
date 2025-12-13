<?php


  /**
   * Validates that open/close tags are balanced after a point.
   *
   * @param string $string Template content.
   * @param string $check  Point to check from.
   *
   * @return bool TRUE if tags are balanced.
   */
  function padOpenCloseOk ( $string, $check) {

    if ( strpos ( $string, $check ) === FALSE )
      return FALSE;

    list ( $dummy, $string ) = explode ( $check, '.' . $string . '.', 2 );

    $tags = padOpenCloseList ( $string );

    return padOpenCloseCount ( $string, $tags);

  }


  /**
   * Extracts list of closing tags from template string.
   *
   * @param string $string Template content.
   *
   * @return array Tag names found as closing tags.
   */
  function padOpenCloseList ( $string ) {

    $tags = [];

    $p1 = strpos($string, '{/', 0);

    while ($p1 !== FALSE) {

      $p2 = strpos($string, '}', $p1);

      if ( $p2 !== FALSE ) {

        $p3 = strpos($string, ' ', $p1);
        if ($p3 !== FALSE and $p3 < $p2 )
          $p2 = $p3;

        $tag = substr($string, $p1+2, $p2-$p1-2);
        if ( padValidTag ($tag) )
          $tags [$tag] = TRUE;

      }

      $p1 = strpos($string, '{/', $p1+1);

    }

    return $tags;

  }


  /**
   * Verifies all tags in list are balanced.
   *
   * @param string $string Template content.
   * @param array  $tags   Tag names to check.
   *
   * @return bool TRUE if all balanced.
   */
  function padOpenCloseCount ( $string, $tags ) {

   foreach ( $tags as $tag => $dummy )
      if ( ! padOpenCloseCountOne ( $string, $tag ) )
        return FALSE;

    return TRUE;

  }


  /**
   * Checks if single tag is balanced (opens equal closes).
   *
   * @param string $string Template content.
   * @param string $tag    Tag name to check.
   *
   * @return bool TRUE if balanced.
   */
  function padOpenCloseCountOne ( $string, $tag ) {

    if ( ( substr_count($string, '{'.$tag.' ' ) + substr_count($string, '{'.$tag.'}' ) )
           <>
         ( substr_count($string, '{/'.$tag.' ') + substr_count($string, '{/'.$tag.'}') ) )
      return FALSE;

    return TRUE;

  }


  /**
   * Checks if tag opens and closes are balanced.
   *
   * @param string $tag    Tag name.
   * @param string $string Template content.
   *
   * @return bool TRUE if balanced.
   */
  function padCheckTag ($tag, $string) {

    return ( substr_count($string, "{".$tag.' ') == substr_count($string, "{/" . $tag.'}') ) ;

  }


  /**
   * Splits string into before and after parts.
   *
   * @param string $needle   Delimiter.
   * @param string $haystack String to split.
   * @param string &$before  Receives part before delimiter.
   * @param string &$after   Receives part after delimiter.
   *
   * @return void
   */
  function padSplit ( $needle, $haystack, &$before, &$after ) {

    $array = explode ( $needle, $haystack, 2 );

    $before = trim ( $array [0] ?? '' );
    $after  = trim ( $array [1] ?? '' );

  }


  /**
   * Extracts text between delimiters.
   *
   * @param string $string  Input string.
   * @param string $open    Opening delimiter.
   * @param string $close   Closing delimiter.
   * @param string &$before Receives text before open.
   * @param string &$between Receives text between delimiters.
   * @param string &$after  Receives text after close.
   *
   * @return bool TRUE if delimiters found.
   */
  function padBetween ( $string, $open, $close, &$before, &$between, &$after ) {

    $before = $between = $after = '';

    $p1 = strpos ( $string, $open );
    if ( $p1 === FALSE ) return FALSE;

    $start = $p1 + strlen($open);
    $p2 = strpos ( $string, $close, $start );
    if ( $p2 === FALSE ) return FALSE;

    if ( $p1 > 0 )
      $before = substr ( $string, 0, $p1 );

    $between = substr ( $string, $start, $p2 - $start );

    $afterPos = $p2 + strlen($close);
    if ( $afterPos < strlen ( $string ) )
      $after = substr ( $string, $afterPos );

    return TRUE;

  }


  /**
   * Splits string by delimiter with entity restoration.
   *
   * Restores escaped entities (&pipe;, &eq;, &comma;) after split.
   *
   * @param string $haystack String to split.
   * @param string $limit    Delimiter character.
   * @param int    $number   Max parts (0 = unlimited).
   *
   * @return array Array of trimmed parts.
   */
  function padExplode ( $haystack, $limit, $number=0 ) {

    if ($number)
      $explode = explode ( $limit, $haystack, $number );
    else
      $explode = explode ( $limit, $haystack );

    foreach ($explode as $key => $value ) {

      $explode [$key] = trim($value);

      if ( $limit == '|' ) $explode [$key] = str_replace ( '&pipe;',  '|', $explode [$key] );
      if ( $limit == '=' ) $explode [$key] = str_replace ( '&eq;',    '=', $explode [$key] );
      if ( $limit == ',' ) $explode [$key] = str_replace ( '&comma;', ',', $explode [$key] );

      if ( $explode [$key] === '' )
        unset ( $explode [$key] );

    }

    return array_values ( $explode );

  }


  /**
   * Sanitizes input for safe logging/display.
   *
   * Removes control characters and truncates to length.
   *
   * @param mixed $input Input value.
   * @param int   $len   Maximum length.
   *
   * @return string Sanitized string.
   */
  function padMakeSafe ( $input, $len=2048 ) {

    if ( is_array($input) or is_object($input) )
      $input = padJson ($input);

    $input = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $input);
    $input = preg_replace('/\s+/', ' ', $input);

    if ( strlen($input) > $len )
      $input = substr ( $input, 0, $len );

    $input = trim($input);

    return $input;

  }


  /**
   * Creates numeric range from string notation.
   *
   * @param string $input     Range string (e.g., "1..10").
   * @param int    $increment Step value.
   *
   * @return array Numeric range array.
   */
  function padGetRange ( $input, $increment=1 ) {

    $parts = padExplode ($input, '..');

    $p1 = $parts[0] ?? '';
    $p2 = $parts[1] ?? '';

    if ( $p2 )
      return range ( $p1, $p2, $increment );
    elseif ( $p1 )
      return range ( 1, $p1, $increment );
    else
      return range ( 1, 10, $increment );

  }


  /**
   * Parses semicolon-separated list, converting numeric strings.
   *
   * @param string $list Semicolon-separated values.
   *
   * @return array Parsed list.
   */
  function padGetList ( $list ) {

    $list = explode ( ';', $list );

    foreach ( $list as $key => $value)
      if ( is_numeric ($value) )
        $list [$key] = intval($value);

    return $list;

  }


?>

<?php

  // The characters a reader sees, for the functions that count and cut text - left, right,
  // mid, substr, max_len and truncate. A flag is two code points, a letter with a combining
  // accent written apart (é as e + U+0301, as macOS file names and many imports have it) is
  // two, a family emoji five: counted in code points left(1) of a flag was a lone regional
  // indicator, of école an e without its accent, and right(1) of a family one child. Text
  // that is no valid UTF-8 is counted as before, in code points.
  //
  // Only as much of the text is read as the cut needs: split into a list of its characters
  // whole, truncate(80) of a 5.6 MB text took the request from 11 MB to 139 MB - past PHP's
  // default limit of 128M - where mb_substr took nothing. ASCII without a CRLF, one
  // character to a byte, is cut by mb_substr; a cut from the front reads its characters
  // with one bounded match (PCRE takes a count up to 65535); a cut from the end splits a
  // window at the end wide enough for it; anything else the whole list, as before.

  function padGraphemes ( $text ) {

    $text = (string) $text;

    if ( $text === '' )
      return [];

    return preg_match_all ( '/\X/u', $text, $match ) === FALSE ? FALSE : $match [0];

  }

  // Whether every character of the text is one byte: ASCII, and no CRLF, which is one.

  function padGraphemeBytes ( $text ) {

    return ! preg_match ( '/[^\x00-\x7F]|\r\n/', $text );

  }

  function padGraphemeLength ( $text ) {

    $list = padGraphemes ( $text );

    return $list === FALSE ? mb_strlen ( (string) $text, 'UTF-8' ) : count ( $list );

  }

  // Whether the text holds more than $count characters - read as far as the one after them.

  function padGraphemeLonger ( $text, $count ) {

    $text  = (string) $text;
    $count = max ( 0, (int) $count );

    if ( strlen ( $text ) <= $count )
      return FALSE;

    if ( padGraphemeBytes ( $text ) )
      return TRUE;

    if ( $count < 65535 ) {
      $found = preg_match ( '/^\X{' . ( $count + 1 ) . '}/u', $text );
      if ( $found !== FALSE )
        return $found === 1;
      return mb_strlen ( $text, 'UTF-8' ) > $count;
    }

    return padGraphemeLength ( $text ) > $count;

  }

  // Cut as mb_substr cuts: a start below 0 counts from the end, a length below 0 leaves that
  // many off the end, NULL takes the rest.

  function padGraphemeCut ( $text, $start, $length = NULL ) {

    $text = (string) $text;

    if ( padGraphemeBytes ( $text ) )
      return mb_substr ( $text, $start, $length, 'UTF-8' );

    if ( $start >= 0 and ( $length === NULL or $length >= 0 ) and $start + (int) $length <= 65535 ) {

      if ( preg_match ( '/^\X{0,' . $start . '}/u', $text, $skip ) === FALSE )
        return mb_substr ( $text, $start, $length, 'UTF-8' );

      $rest = substr ( $text, strlen ( $skip [0] ) );

      if ( $length === NULL )
        return $rest;

      preg_match ( '/^\X{0,' . $length . '}/u', $rest, $take );

      return $take [0];

    }

    if ( $start < 0 and ( $length === NULL or $length >= 0 ) )
      return padGraphemeEnd ( $text, - $start, $length );

    return padGraphemeSlice ( $text, $start, $length );

  }

  function padGraphemeSlice ( $text, $start, $length ) {

    $list = padGraphemes ( $text );

    if ( $list === FALSE )
      return mb_substr ( (string) $text, $start, $length, 'UTF-8' );

    return implode ( '', array_slice ( $list, $start, $length ) );

  }

  // The last $count characters, then $length of them: split from a window at the end, from
  // a character boundary on, that holds more of them than asked - the first one of a window
  // can be the tail of a character cut in two. A window that starts inside a run of
  // regional indicators is widened, since a flag is the pair counted from the run's start.

  function padGraphemeEnd ( $text, $count, $length ) {

    // A text that is no valid UTF-8 is cut in code points, as every other cut of it is: a
    // window at its end can be valid where the text is not, and right(1) gave a whole
    // character when the broken byte stood more than a window before the end and a code
    // point when it stood in it.

    if ( ! mb_check_encoding ( $text, 'UTF-8' ) )
      return mb_substr ( $text, - $count, $length, 'UTF-8' );

    $size = max ( 4096, $count * 64 );

    while ( $size < strlen ( $text ) ) {

      $from = strlen ( $text ) - $size;

      while ( $from > 0 and ( ord ( $text [$from] ) & 0xC0 ) == 0x80 )
        $from--;

      $window = substr ( $text, $from );

      if ( ! preg_match ( '/^[\x{1F1E6}-\x{1F1FF}]/u', $window ) ) {

        $list = padGraphemes ( $window );

        if ( $list === FALSE )
          break;

        if ( count ( $list ) > $count + 1 )
          return implode ( '', array_slice ( $list, - $count, $length ) );

      }

      $size *= 4;

    }

    return padGraphemeSlice ( $text, - $count, $length );

  }

?>

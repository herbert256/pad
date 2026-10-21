<?php

  // The characters a reader sees, for the functions that count and cut text - left, right,
  // mid, substr, max_len and truncate. A flag is two code points, a letter with a combining
  // accent written apart (é as e + U+0301, as macOS file names and many imports have it) is
  // two, a family emoji five: counted in code points left(1) of a flag was a lone regional
  // indicator, of école an e without its accent, and right(1) of a family one child. Text
  // that is no valid UTF-8 is counted as before, in code points.

  function padGraphemes ( $text ) {

    $text = (string) $text;

    if ( $text === '' )
      return [];

    return preg_match_all ( '/\X/u', $text, $match ) === FALSE ? FALSE : $match [0];

  }

  function padGraphemeLength ( $text ) {

    $list = padGraphemes ( $text );

    return $list === FALSE ? mb_strlen ( (string) $text, 'UTF-8' ) : count ( $list );

  }

  // Cut as mb_substr cuts: a start below 0 counts from the end, a length below 0 leaves that
  // many off the end, NULL takes the rest.

  function padGraphemeCut ( $text, $start, $length = NULL ) {

    $list = padGraphemes ( $text );

    if ( $list === FALSE )
      return mb_substr ( (string) $text, $start, $length, 'UTF-8' );

    return implode ( '', array_slice ( $list, $start, $length ) );

  }

?>
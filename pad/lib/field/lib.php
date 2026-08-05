<?php

  // Works out which nesting level a lookup should be aimed at, for the callers in
  // lib/field/.
  //
  //   padFieldGetLevel     maps the prefix of a prefix:field name to a level index: a tag
  //                        name given with name=, then a number (negative counts back from
  //                        the current level), then a tag name; the current level if none
  //                        of that matches
  //   padTagFieldSearch    the same question asked as a yes/no - does such a level exist
  //   padFieldFirstParmTag nearest enclosing level that can carry options, skipping {if}
  //                        and {case} and levels that are themselves tags
  //   padFieldFirstNonTag  nearest enclosing level that is not a tag, optionally starting
  //                        $lvl levels further out - used for property lookups so that a
  //                        property reads from the data tag, not the tag calling it
  //
  // Both searches stop above level 0 and fall back to $pad-1 rather than failing.

  function padFieldGetLevel  ( $search ) {

    global $pad, $padName, $padTag;

    if ( trim($search) == '' )
      return $pad;

    for ( $i=$pad; $i; $i-- )
      if ( $padName [$i] == $search )
        return $i;

    if ( is_numeric($search) and $search < 0 )
      return $pad + $search;

    if ( is_numeric($search) )
      return $search;

    for ( $i=$pad; $i; $i-- )
      if ( $padTag [$i] == $search )
        return $i;

    return $pad;

  }

  function padTagFieldSearch ( $search ) {

    global $pad, $padName, $padTag;

    if ( trim($search) == '' )
      return FALSE;

    for ( $i=$pad; $i; $i-- )
      if ( $padName [$i] == $search )
        return TRUE;

    if ( is_numeric($search) and $search < 0 )
      return TRUE;

    if ( is_numeric($search) )
      return TRUE;

    for ( $i=$pad; $i; $i-- )
      if ( $padTag [$i] == $search )
        return TRUE;

    return FALSE;

  }

  function padFieldFirstParmTag ($flag=0) {

    global $pad, $padType, $padTag;

    $start = ($flag) ? $pad-1 : $pad;

    for ($i=$start; $i; $i--)
      if ( $padTag [$i] != 'if' and $padTag [$i] != 'case' and $padType[$i] != 'tag' )
        return $i;

    return $pad - 1;

  }

  function padFieldFirstNonTag ($lvl=0) {

    global $pad, $padType;

    $start = $pad-$lvl;

    for ($i=$start; $i; $i--)
      if ( $padType[$i] != 'tag' )
        return $i;

    return $pad - 1;

  }

?>
<?php

  // Small helpers shared by the @ resolver.
  //
  //   padAtDataNew  defines a data store on demand through padData(), searches it, and
  //                 drops it again when the path was not found there
  //   padAtIdx      turns the target of a reference into a level index: a level name, a
  //                 negative offset from $pad (shifted by $cor), a plain level number or
  //                 a tag name, searching from the current level down; FALSE if no match
  //   padAtKey      maps a 1-based ordinal onto the nth key of an associative array
  //   padAtSetTag   marks the current level as type 'tag' once a property has resolved

   function padAtDataNew ( $name, $names ) {

    global $padDataStore;

    $padDataStore [$name] = padData ($name);

    $check = padAtSearch ( $padDataStore [$name], $names );

    if ( $check === INF )
      unset ( $padDataStore [$name] );

    return $check;

  }

  function padAtIdx ( $level, $cor ) {

    global $pad, $padName, $padTag;


    for ( $i=$pad; $i>-1; $i-- )
      if ( $padName [$i] == $level )
        return $i;

    // A relative -N counts enclosing levels from the tag the reference is written in. The
    // resolution may run before that tag's level exists (type detection, $cor 0, $pad still
    // the enclosing level) or after it opened ($cor 1, $pad the tag's own level); one step
    // separates the two, so 1-$cor lands both on the same target. The old $pad+$level+$cor
    // was one level short on both routes: {first@-2} answered about the level it stood in,
    // where {first -2} over the same nesting answered about the loop.

    if ( strlen($level) > 1 and substr($level,0,1) == '-' and is_numeric(substr($level,1)) ) {

      $idx = $pad + $level + 1 - $cor;

      if ( $idx > 0 and $idx <= $pad )
        return $idx;

    }

    if ( is_numeric ($level) )
      if ($level >= 0 and $level <= $pad )
        return $level;

    for ( $i=$pad; $i>-1; $i-- )
      if ( $padTag [$i] == $level )
        return $i;

    return FALSE;

  }

  function padAtKey ( $search, $index ) {

    if ( ! ctype_digit ( $index ) )             return '';
    if ( array_is_list ( $search ) )            return '';
    if ( array_key_exists ( $index, $search ) ) return '';

    $keys = array_keys ( $search );

    return $keys [ $index - 1 ] ?? '';

  }

  // Marks the current level as answered-by-at, which the field walkers in lib/field/lib.php
  // read as "skip this one". Skipped while an expression is being evaluated: a property
  // resolving inside another tag's parameters - {echo $option.opt@true} - used to stamp
  // that tag itself, before its own type had run, and the level then went looking for
  // types/tag.php, which does not exist. A reference standing as a tag still marks its
  // level the way it always has.

  function padAtSetTag () {

    global $pad, $padType, $padEvalBusy;

    if ( $padEvalBusy ?? 0 )
      return;

    $padType [$pad] = 'tag' ;

  }

?>
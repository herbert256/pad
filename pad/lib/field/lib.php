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
  //   padFieldFirstNonTag  nearest enclosing level that is not a tag, nor a part of its row
  //                        - an {if}, a {case}, a {guest}, a snippet and the like
  //                        (padLevelInRow), optionally starting $lvl levels further out - used for
  //                        property lookups so that a property reads from the data tag, not
  //                        the tag calling it
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

  // At page level the start can be below 0: the walk stops at 0 rather than counting on
  // through the negatives, and the fallback is the root level - it was -1, an undefined key.
  //
  // A {slot} level is looked past like an if: a {#title} in a slot's default belongs to the
  // tag whose template it is in. A slot level rendering a fill hands the search on to below
  // the tag the fill was given to - the fill is the caller's text (lib/slot.php).

  function padFieldFirstParmTag ($flag=0) {

    global $pad, $padAtTag, $padTag, $padType, $padSlotFrom;

    $start = ($flag) ? $pad-1 : $pad;

    for ($i=$start; $i > 0; $i--) {

      if ( isset ( $padSlotFrom [$i] ) ) {
        $i = $padSlotFrom [$i];
        continue;
      }

      if ( $padTag [$i] == 'slot' and ( $padType [$i] ?? '' ) == 'pad' )
        continue;

      if ( $padTag [$i] != 'if' and $padTag [$i] != 'case' and ! ( $padAtTag [$i] ?? FALSE ) )
        return $i;

    }

    return max ( 0, $pad - 1 );

  }

  // An {if} or a {case} is no loop: a property asked for inside one - {&current},
  // {current@}, {property:first} - belongs to the loop around it, as an option inside one
  // belongs to the tag around it (padFieldFirstParmTag). It was read from the {if} or the
  // {case} level itself, which has one occurrence: current was 1 on every row, first was
  // always true.
  //
  // Nor is any other level that renders its content once for the row it stands in
  // (padLevelInRow, lib/level.php) - an {ifchanged}, a {guest}, {auth}, {can} or {feature},
  // a {spaceless}, a {fragment}, a snippet, a custom tag: only if and case were looked past,
  // and {items}{ifchanged $g}{notFirst@}</ul>{/notFirst@}... never closed the list before a
  // new heading, {xs}{guest}{&current}{/guest} was 1 on every row.
  //
  // That holds for the properties of the rows. The properties of a tag itself - its name,
  // its options, parameters and variables (padTagOwnProperties) - belong to the tag they
  // are written in: {pad name='myTag'}{&name}{/pad} is myTag, and only an {if} or a {case}
  // is looked past for them, as before.

  const padTagOwnProperties = [ 'name', 'option', 'options', 'parameter', 'parameters', 'variable', 'variables' ];

  function padFieldFirstNonTag ( $lvl = 0, $property = '' ) {

    global $pad, $padAtTag, $padTag;

    $start = $pad-$lvl;
    $own   = in_array ( $property, padTagOwnProperties, TRUE );

    // A property pair is looked past by padLevelInRow when it renders once - {first@xs},
    // {notFirst@} - but one that iterates a data set - {fields@xs}, {keys@xs}, {data@xs},
    // {options@xs} - is the loop there: passed over as a property tag, {notFirst@}, {/notFirst@}
    // inside {fields@xs} read the row of xs and put no comma between the fields of its first
    // row. A tag's own properties still pass every property pair, as an option does.

    for ($i=$start; $i > 0; $i--)
      if ( $own ? ! ( $padAtTag [$i] ?? FALSE ) and ! in_array ( $padTag [$i] ?? '', [ 'if', 'case' ] )
                : ! padLevelInRow ( $i ) )
        return $i;

    return max ( 0, $pad - 1 );

  }

?>

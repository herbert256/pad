<?php

  // Front door to field resolution: given a name written in a template, find the value it
  // refers to in the current level context. Everything that reads a {$field}, an option, a
  // tag parameter or an iteration property comes through here.
  //
  // The nine wrappers are one function, padField, with a numeric $type that picks both
  // where to look and how the answer is shaped:
  //
  //   1 padFieldCheck   2 padFieldValue   scalar field - exists / value
  //   3 padArrayCheck   4 padArrayValue   array field  - exists / value
  //   5 padOptCheck     6 padOptValue     tag option, resolved via padParm
  //   7 padTagCheck     8 padTagValue     tag property, resolved via padTag
  //   9 padFieldNull                      is the field present and NULL
  //
  // A name containing @ or . is at-syntax (name@tag, dotted paths) and goes to padFieldAt,
  // which normalises the missing half to @* and hands over to the at/ subsystem. Otherwise
  // a leading prefix: selects the level to search - padFieldGetLevel for an explicit one,
  // padFieldFirstParmTag for options, padFieldFirstNonTag for properties, the current level
  // otherwise - and padFieldPrefix or padFieldLevel does the lookup.
  //
  // INF is the internal "not found" marker, never a value; padField retries a plain field
  // as a property and then as an option before shaping INF into ''/FALSE for the caller.
  // The name 'pad' is reserved and always yields $padGo, the app's own URL prefix.
  //
  // padRawValue, padUrlValue and padJsonEscape are output helpers on top of padFieldValue,
  // for embedding a value in template text, in a query string and in HTML respectively.

  function padFieldCheck   ( $parm, $lvl=0 ) { return padField ( $parm, 1, $lvl  ); }

  function padFieldValue   ( $parm, $lvl=0 ) { return padField ( $parm, 2, $lvl  ); }

  function padArrayCheck   ( $parm, $lvl=0 ) { return padField ( $parm, 3, $lvl  ); }

  function padArrayValue   ( $parm, $lvl=0 ) { return padField ( $parm, 4, $lvl  ); }

  function padOptCheck     ( $parm )         { return padField ( $parm, 5        ); }

  function padOptValue     ( $parm, $lvl=0 ) { return padField ( $parm, 6, $lvl  ); }

  function padTagCheck     ( $parm         ) { return padField ( $parm, 7        ); }

  function padTagValue     ( $parm, $lvl=0 ) { return padField ( $parm, 8, $lvl  ); }

  function padFieldNull    ( $parm )         { return padField ( $parm, 9        ); }

  function padField ( $field, $type, $lvl=0 ) {

    global $pad, $padGo, $padInfo;

    if ( $field == 'pad' )
      return $padGo;

    if ( $padInfo )
      include PAD . 'events/fieldStart.php';

    if ( str_contains ( $field, '@' ) or str_contains ( $field, '.' ) ) {

      $value = padFieldAt ( $field, $lvl );

      if ( $padInfo )
        include PAD . 'events/fieldAt.php';

    } else {

      if ( str_contains ($field, ':' ) )
        list ( $prefix, $field ) = explode (':', $field, 2);
      else
        $prefix = '';

      if     ( $prefix                   ) $idx = padFieldGetLevel ($prefix);
      elseif ( in_array ( $type, [5,6] ) ) $idx = padFieldFirstParmTag ($lvl);
      elseif ( in_array ( $type, [7,8] ) ) $idx = padFieldFirstNonTag ($lvl);
      else                                 $idx = $pad;

      padSplit ( ':', $field, $field, $parm  );

      if     ( $type ==  5 ) $value = padParm        ( $field, $idx, $type );
      elseif ( $type ==  6 ) $value = padParm        ( $field, $idx, $type );
      elseif ( $type ==  7 ) $value = padTag         ( $field, $idx, $type, $parm );
      elseif ( $type ==  8 ) $value = padTag         ( $field, $idx, $type, $parm );
      elseif ( $prefix     ) $value = padFieldPrefix ( $field, $idx, $type, $prefix );
      else                   $value = padFieldLevel  ( $field, $type );

      if ( $value === INF and $type == 1 ) $value = padTag  ( $field, $idx, 7, $parm );
      if ( $value === INF and $type == 2 ) $value = padTag  ( $field, $idx, 8, $parm );
      if ( $value === INF and $type == 1 ) $value = padParm ( $field, $idx, 5);
      if ( $value === INF and $type == 2 ) $value = padParm ( $field, $idx, 6 );

      if ( $padInfo )
        include PAD . 'events/fieldClassic.php';

    }

    if     ($type ==  1) $return = ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type ==  2) $return = ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? ''    : $value;
    elseif ($type ==  3) $return = ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type ==  4) $return = ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? []    : $value;
    elseif ($type ==  5) $return = ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type ==  6) $return = ( $value === INF                                                ) ? ''    : $value;
    elseif ($type ==  7) $return = ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type ==  8) $return = ( $value === INF                                                ) ? ''    : $value;
    elseif ($type ==  9) $return = ( $value === NULL                                               ) ? TRUE  : FALSE;

    if ( $padInfo )
      include PAD . 'events/fieldEnd.php';

    return $return;

  }

  function padFieldAt ( $field, $lvl ) {

    if ( str_starts_with ($field, '@') and substr_count($field, '@') == 1 )
      $field .= '@*';

    if ( str_contains ( $field, '.' ) and ! str_contains ( $field, '@' )  )
      $field .= '@*';

    return padAt ( $field, $lvl );

  }

  function padRawValue ( $parm ) {

    return str_replace ( '}', '&close;', padFieldValue ($parm) );

  }

  function padUrlValue ( $parm )  {

    return "&$parm=" . urlencode ( padFieldValue ( $parm ) );

  }

  function padJsonEscape ( $parm )  {

    return htmlspecialchars ( json_encode ( $parm ), ENT_QUOTES, 'UTF-8' );

  }

?>
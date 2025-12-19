<?php


  /** Checks if a scalar field exists. @see padField */
  function padFieldCheck   ( $parm, $lvl=0 ) { return padField ( $parm, 1, $lvl  ); }

  /** Gets a scalar field value. @see padField */
  function padFieldValue   ( $parm, $lvl=0 ) { return padField ( $parm, 2, $lvl  ); }

  /** Checks if an array field exists. @see padField */
  function padArrayCheck   ( $parm, $lvl=0 ) { return padField ( $parm, 3, $lvl  ); }

  /** Gets an array field value. @see padField */
  function padArrayValue   ( $parm, $lvl=0 ) { return padField ( $parm, 4, $lvl  ); }

  /** Checks if an option exists. @see padField */
  function padOptCheck     ( $parm )         { return padField ( $parm, 5        ); }

  /** Gets an option value. @see padField */
  function padOptValue     ( $parm )         { return padField ( $parm, 6        ); }

  /** Checks if a tag property exists. @see padField */
  function padTagCheck     ( $parm         ) { return padField ( $parm, 7        ); }

  /** Gets a tag property value. @see padField */
  function padTagValue     ( $parm, $lvl=0 ) { return padField ( $parm, 8, $lvl  ); }

  /** Checks if a field is NULL. @see padField */
  function padFieldNull    ( $parm )         { return padField ( $parm, 9        ); }


  /**
   * Main field value retrieval function.
   *
   * Routes field requests based on type: scalar fields (1-2),
   * array fields (3-4), options (5-6), tag properties (7-8),
   * null check (9). Handles @ and . notation for path access.
   *
   * @param string $field The field name or path expression.
   * @param int    $type  The type of operation (1-9).
   * @param int    $lvl   Optional level offset.
   *
   * @return mixed Result based on type: bool for checks, value for gets.
   */
  function padField ( $field, $type, $lvl=0 ) {

 #   if ( str_contains ( $field, ':' ) and ( $type == 1 or $type == 2) )
 #     $type = $type + 6;

    if ( $field == 'pad' )
      return $GLOBALS ['padGo'];

    if ( $GLOBALS ['padInfo'] )
      include PAD . 'events/fieldStart.php';

    if ( str_contains ( $field, '@' ) or str_contains ( $field, '.' ) ) {

      $value = padFieldAt ( $field, $lvl );

      if ( $GLOBALS ['padInfo'] )
        include PAD . 'events/fieldAt.php';

    } else {

      if ( str_contains ($field, ':' ) )
        list ( $prefix, $field ) = explode (':', $field, 2);
      else
        $prefix = '';

      if     ( $prefix                   ) $idx = padFieldGetLevel ($prefix);
      elseif ( in_array ( $type, [5,6] ) ) $idx = padFieldFirstParmTag ();
      elseif ( in_array ( $type, [7,8] ) ) $idx = padFieldFirstNonTag ($lvl);
      else                                 $idx = $GLOBALS ['pad'];

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

      if ( $GLOBALS ['padInfo'] )
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

    if ( $GLOBALS ['padInfo'] )
      include PAD . 'events/fieldEnd.php';

    return $return;

  }


  /**
   * Resolves field paths with @ and . notation.
   *
   * Prepares field expression for padAt processing by appending
   * @* wildcard when needed for proper path resolution.
   *
   * @param string $field The field path expression.
   * @param int    $lvl   Level offset for resolution.
   *
   * @return mixed The resolved field value.
   */
  function padFieldAt ( $field, $lvl ) {

    if ( str_starts_with ($field, '@') and substr_count($field, '@') == 1 )
      $field .= '@*';

    if ( str_contains ( $field, '.' ) and ! str_contains ( $field, '@' )  )
      $field .= '@*';

    return padAt ( $field, $lvl );

  }


  /**
   * Gets field value with closing braces escaped.
   *
   * Retrieves field value and escapes } to prevent template parsing.
   *
   * @param string $parm The field name.
   *
   * @return string The value with } replaced by &close;
   */
  function padRawValue ( $parm ) {

    return str_replace ( '}', '&close;', padFieldValue ($parm) );

  }


  /**
   * Gets field value formatted as URL parameter.
   *
   * Returns the field name and URL-encoded value as a query string
   * fragment in the format &name=value.
   *
   * @param string $parm The field name.
   *
   * @return string URL parameter string.
   */
  function padUrlValue ( $parm )  {

    return "&$parm=" . urlencode ( padFieldValue ( $parm ) );

  }


?>
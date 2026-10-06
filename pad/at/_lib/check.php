<?php

  // Syntax check for an @ reference, run by padAt() before any lookup is attempted, so
  // that a stray @ in ordinary text is never treated as a reference.
  //
  // padAtCheck demands exactly one @, no whitespace, and a non-empty path on either side
  // that neither starts nor ends with a dot; @* is validated as if it were @1. Each part
  // of the target must pass padAtCheckPart (numeric, alphabetic or a valid store name)
  // and each element of the path padAtCheckNamePart, which additionally allows the *
  // wildcard and the search forms 3<, 2> and name=value / name<>value. padAtCheckCondition
  // vets one such comparison and returns TRUE when the operator does not occur at all.

 function padAtCheck ( $field ) {

    if ( str_ends_with ( $field, '@' ) )
      $field .= '*';

    if ( str_contains($field, '@*') )
      return padAtCheck ( str_replace ( '@*', "@1", $field ) );

    $field = rtrim ( $field );

    // A leading ~ is the whitespace-control sigil, which padTildeStrip removes before the
    // scanner reaches the tag, so the engine never sees it here - but the reference's syntax
    // highlighter checks raw source, where {~notLast@contact} still carries it. It is not
    // part of the name, so it is dropped before the name is validated; the stricter name
    // check below would otherwise fail ~notLast and the highlighter lose the colour of a
    // valid tag.

    if ( str_starts_with ( $field, '~' ) )
      $field = substr ( $field, 1 );

    if ( preg_match ( '/\s/', $field  ) ) return FALSE;
    if ( substr_count($field, '@') != 1 ) return FALSE;

    padSplit ( '@', $field, $before, $after );

    if ( ! strlen ( $before )            ) return FALSE;
    if ( ! strlen ( $after  )            ) return FALSE;
    if ( str_starts_with ( $before, '.') ) return FALSE;
    if ( str_starts_with ( $after,  '.') ) return FALSE;
    if ( str_ends_with   ( $before, '.') ) return FALSE;
    if ( str_ends_with   ( $after,  '.') ) return FALSE;

    $names = padExplode ( $before, '.' );
    $parts = padExplode ( $after,  '.' );

    foreach ( $parts as $part)
      if ( ! padAtCheckPart ($part) )
        return FALSE;

    foreach ( $names as $part)
      if ( ! padAtCheckNamePart ($part) )
        return FALSE;

    return TRUE;

  }

  function padAtCheckPart ( $part ) {

    if ( is_numeric  ( $part ) ) return TRUE;
    if ( ctype_alpha ( $part ) ) return TRUE;
    if ( ctype_digit ( $part ) ) return TRUE;
    if ( padAtValid  ( $part ) ) return TRUE;

    return FALSE;

  }

  function padAtCheckNamePart ( $part ) {

    if ( ctype_alpha ( $part) ) return TRUE;
    if ( ctype_digit ( $part) ) return TRUE;
    if ( $part == '*')          return TRUE;
    if ( $part == '<')          return TRUE;
    if ( $part == '>')          return TRUE;

    if ( strlen($part) > 1 ) {
      $check1 = substr ( $part, 0, 1 );
      $check2 = substr ( $part, 1    );
      if ( $check1 == '<' and ctype_digit ( $check2) ) return TRUE;
      if ( $check1 == '>' and ctype_digit ( $check2) ) return TRUE;
    }

    // The positional search forms 3< (third from the start) and 2> (second from the end),
    // which padAtSearchIdx reads - a run of digits then one < or >. They used to pass only
    // because padAtCheckCondition answered TRUE for a part with no operator; named here so
    // that blanket yes could become a no.

    if ( preg_match ( '/^\d+[<>]$/', $part ) ) return TRUE;

    if ( padAtCheckCondition ( $part, '<>' ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '<=' ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '>=' ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '>'  ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '<'  ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '='  ) ) return TRUE;

    if ( padAtValid ( $part ) ) return TRUE;

    return FALSE;

  }

  function padAtCheckCondition ( $part, $condition ) {

    // This helper vets the name=value / name<>value search forms. When its operator is not
    // in the part, the part is not that form - so the answer is "no", not "yes": returning
    // TRUE here let every check pass and made padAtCheckNamePart accept any part at all, so
    // padValid('a/../../etc/x@y') and padValid('x*?[@q') were TRUE. With a plain no, the part
    // falls to the padAtValid test below, which a path or a glob character fails.

    if ( ! str_contains ( $part, $condition ) )
      return FALSE;

    $parts = explode ( $condition, $part );

    if ( count ( $parts ) != 2       ) return FALSE;
    if ( ! strlen ( $parts [0] )     ) return FALSE;
    if ( ! strlen ( $parts [1] )     ) return FALSE;
    if ( ! padAtValid ( $parts [0] ) ) return FALSE;

    return TRUE;

  }

?>

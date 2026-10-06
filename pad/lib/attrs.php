<?php

  // The HTML attribute helpers behind {attrs} and {classes}. Both tags take their items raw -
  // level/parms/parms.php routes every item to the positional path and parameter.php leaves
  // it unevaluated - because an item is an attribute, not an engine option: content=,
  // data-id= and aria-expanded= are attribute names here, and as options they would have
  // reached the content store, the data option or the expression parser.
  //
  // padAttrsItems splits the raw items into [ name, expression ] pairs at the first = (a
  // name is an HTML attribute name; an item without one is [ '', expression ]).

  // Whether the tag on this level takes its items raw - the built-in {attrs}, {classes},
  // {trans}, {debug}, {parms}, {assert}, and the form tags {form}, {input} and {textarea}.

  function padParmsRaw () {

    global $pad, $padTag, $padType;

    return in_array ( $padTag [$pad] ?? '', [ 'attrs', 'classes', 'trans', 'debug', 'parms', 'form', 'input', 'textarea', 'assert' ] ) and ( $padType [$pad] ?? '' ) == 'pad';

  }

  const padAttrsBoolean = [
    'allowfullscreen', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default',
    'defer', 'disabled', 'formnovalidate', 'hidden', 'inert', 'ismap', 'itemscope', 'loop',
    'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly',
    'required', 'reversed', 'selected'
  ];

  function padAttrsItems () {

    global $pad, $padParms;

    $items = [];

    foreach ( $padParms [$pad] as $one ) {

      $item = trim ( $one ['padPrmOrg'] ?? '' );

      if ( $item !== '' )
        $items [] = padAttrsSplit ( $item );

    }

    return $items;

  }

  // One item as [ name, expression ] - split at its first = when what stands before it is an
  // attribute name - or [ '', item ]. The form rules read from a template before it renders
  // (lib/form.php) split their items with it too, so both read a name the same way.

  function padAttrsSplit ( $item ) {

    if ( preg_match ( '/^([A-Za-z_:@][-A-Za-z0-9_:.@]*)\s*=(?!=)\s*(.*)$/s', $item, $match ) )
      return [ $match [1], $match [2] ];

    return [ '', $item ];

  }

  // Whether an item is a bare word - an attribute written without a value, required.

  function padAttrsBare ( $item ) {

    return (bool) preg_match ( '/^[A-Za-z_:@][-A-Za-z0-9_:.@]*$/', $item );

  }

  // The value of an item. $extra naming an array - of the page or of the row - is that
  // array: the expression evaluator reads a $field as one value only, so the documented
  // {attrs $extra} and {classes $list} ended in "there is no field named", and so did a
  // named item - class=$list, joined with spaces, or active=$items, in when not empty.

  function padAttrsValue ( $expr ) {

    if ( preg_match ( '/^\$([A-Za-z_][A-Za-z0-9_]*)$/', trim ( $expr ), $match )
         and ! padFieldCheck ( $match [1] ) and padArrayCheck ( $match [1] ) )
      return padArrayValue ( $match [1] );

    return padEval ( $expr );

  }

  function padAttrsTrue ( $value ) {

    if ( is_array ( $value ) )
      return count ( $value ) > 0;

    return (bool) $value;

  }

  // {attrs disabled=$busy, title=$help, aria-expanded=$open}: the attributes, quoted and
  // escaped, space-separated. A boolean HTML attribute - disabled, checked, ... - is written
  // bare when its value is true and left out when it is not. Any other attribute is left
  // out for FALSE and NULL, written bare for TRUE, and otherwise written with its value -
  // 'false' stays aria-expanded="false" - an array joined with spaces. A name alone is a
  // bare attribute: {attrs required}. An item without a name is an expression whose array
  // value adds its keys as attributes: {attrs $extra}. A key that is no attribute name is
  // left out - written as it was, x" onmouseover="... ended the attribute list's quoting.

  function padAttrs () {

    $out = [];

    foreach ( padAttrsItems () as [ $name, $expr ] ) {

      if ( $name === '' and preg_match ( '/^[A-Za-z_:@][-A-Za-z0-9_:.@]*$/', $expr ) ) {
        $out [ strtolower ( $expr ) ] = $expr;
        continue;
      }

      if ( $name === '' ) {
        $value = padAttrsValue ( $expr );
        if ( is_array ( $value ) )
          foreach ( $value as $key => $one )
            if ( padAttrsBare ( (string) $key ) )
              padAttrsOne ( $out, (string) $key, $one );
        continue;
      }

      padAttrsOne ( $out, $name, padAttrsValue ( $expr ) );

    }

    return implode ( ' ', $out );

  }

  function padAttrsOne ( &$out, $name, $value ) {

    $key = strtolower ( $name );

    unset ( $out [$key] );

    if ( in_array ( $key, padAttrsBoolean ) ) {
      if ( padAttrsTrue ( $value ) )
        $out [$key] = $name;
      return;
    }

    if ( $value === FALSE or $value === NULL )
      return;

    if ( $value === TRUE ) {
      $out [$key] = $name;
      return;
    }

    if ( is_array ( $value ) )
      $value = implode ( ' ', array_filter ( array_map ( 'strval', $value ), 'strlen' ) );

    $out [$key] = $name . '="' . htmlspecialchars ( (string) $value, ENT_QUOTES, 'UTF-8' ) . '"';

  }

  // {classes 'panel', active=$isActive, invalid=$hasErrors}: the class names, deduplicated
  // and space-separated. An item without a name is an expression whose value - a string of
  // one or more names, or an array of them - is always in; name=condition puts that name in
  // when the condition holds.

  function padClasses () {

    $out = [];

    foreach ( padAttrsItems () as [ $name, $expr ] ) {

      if ( $name !== '' ) {
        if ( padAttrsTrue ( padAttrsValue ( $expr ) ) )
          $out [] = $name;
        continue;
      }

      $value = padAttrsValue ( $expr );

      foreach ( is_array ( $value ) ? $value : [ $value ] as $one )
        foreach ( preg_split ( '/\s+/', trim ( (string) $one ) ) as $class )
          if ( $class !== '' )
            $out [] = $class;

    }

    return htmlspecialchars ( implode ( ' ', array_unique ( $out ) ), ENT_QUOTES, 'UTF-8' );

  }

?>

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
  // {trans}, {debug}, {parms}, {assert}, the form tags {form}, {input} and {textarea},
  // {can} and {cannot}, and {jsonview}, whose values may be arrays - a row, an answer of an
  // API - that an expression cannot hold.

  function padParmsRaw () {

    global $pad, $padTag, $padType;

    return in_array ( $padTag [$pad] ?? '', [ 'attrs', 'classes', 'trans', 'debug', 'parms', 'form', 'input', 'textarea', 'assert', 'can', 'cannot', 'jsonview' ] ) and ( $padType [$pad] ?? '' ) == 'pad';

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

  // Whether an attribute an array brought may be written: the array's keys and values are
  // data - often a visitor's, decoded from JSON. An event handler is left out - on... of
  // HTML - and so is every directive a script library runs as script, in each spelling it
  // reads: @ and : of Alpine and Vue, x-... (x-on, x-bind, x-init, x-data, x-html ...) and
  // v-... of them, hx-... and data-hx-... of htmx (hx-on, hx-vals="js:..."), ng-, ng:,
  // data-ng- of Angular, wire: of Livewire, _, script and data-script of hyperscript and
  // data-bind of Knockout. So is an iframe's srcdoc, whose HTML the browser runs
  // same-origin; a URL attribute is judged by padAttrsUrlSafe. The names written in the
  // template are the author's and are not judged.

  function padAttrsSafe ( $name, $value ) {

    $name = strtolower ( $name );

    if ( preg_match ( '/^(on|[@:_]|(data-)?(x|v|hx|ng)[-:_]|wire:)|^((data-)?script|data-bind|srcdoc)$/', $name ) )
      return FALSE;

    if ( in_array ( $name, [ 'href', 'src', 'action', 'formaction', 'xlink:href', 'poster',
                             'cite', 'background', 'data', 'ping', 'longdesc', 'manifest' ] ) )
      return padAttrsUrlSafe ( $value );

    return TRUE;

  }

  // Whether a URL attribute's value names no scheme that runs script. The value is judged as
  // padAttrsOne writes it - an array joined with spaces - and its control characters taken
  // out (a browser drops them, so java\tscript: is javascript:); the deny list is the
  // schemes that run - javascript:, vbscript:, and data: unless it is a data:image/ - rather
  // than an allow list, which dropped a real data:image/ source and an sms: link. The & of
  // an entity is escaped when the value is written, so a reference stays inert text and is
  // not decoded here.

  function padAttrsUrlSafe ( $value ) {

    if ( is_array ( $value ) )
      $value = implode ( ' ', array_filter ( array_map ( 'strval', $value ), 'strlen' ) );
    elseif ( ! is_scalar ( $value ) or is_bool ( $value ) )
      return TRUE;

    $plain = preg_replace ( '/[\x00-\x20]/', '', (string) $value );

    if ( preg_match ( '/^([a-zA-Z][a-zA-Z0-9+.\-]*):/', $plain, $m ) ) {

      $scheme = strtolower ( $m [1] );

      if ( in_array ( $scheme, [ 'javascript', 'vbscript' ], TRUE ) )
        return FALSE;

      if ( $scheme == 'data' and ! preg_match ( '#^data:image/#i', $plain ) )
        return FALSE;

    }

    return TRUE;

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
            if ( padAttrsBare ( (string) $key ) and padAttrsSafe ( (string) $key, $one ) )
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

    // The stand-in of a quote or the backslash from the request is escaped as that character
    // (padUnprotectQuotes), which exits.php would otherwise make a live quote after this
    // escaping - closing the attribute data-x="{$v}" built and adding an event handler.

    $out [$key] = $name . '="' . htmlspecialchars ( padUnprotectQuotes ( (string) $value ), ENT_QUOTES, 'UTF-8' ) . '"';

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

    return htmlspecialchars ( padUnprotectQuotes ( implode ( ' ', array_unique ( $out ) ) ), ENT_QUOTES, 'UTF-8' );

  }

?>

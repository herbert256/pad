<?php

  // A JSON value, or a PHP array, as a tree to fold open and shut - the {jsonview} tag.
  //
  //   {jsonview $response}                 a page array, a row, a JSON text
  //   {jsonview 'orders', open=1}          a {data} store, a page array or a _data file
  //   {jsonview title='Answer'} {"id": 7, "tags": ["a", "b"]} {/jsonview}
  //
  // Every object and list is a <details> with its key in the <summary>: the browser folds
  // it, without a script, and a keyboard opens it as any disclosure. Folded, the summary
  // says how many keys or items are inside. Strings, numbers, booleans and null each have a
  // colour - CSS custom properties with light and dark defaults on .pad-jsonview:
  // --pad-jsonview-key, -string, -number, -boolean, -null, -muted, -line and -surface.
  //
  // padJsonviewValue  the value an item of the tag stands for: a $field holding a list is
  //                   that list, which an expression cannot hold; a text is the text
  // padJsonviewNamed  the data a name stands for: a {data} store, a sequence store, a page
  //                   array or a _data file - a .json file read as it is written
  // padJsonviewText   a JSON text decoded, NULL when it is none
  // padJsonviewError  why a text is no JSON
  // padJsonviewHtml   the tree; levels below $open start folded
  // padJsonviewNode   one key and its value

  function padJsonviewValue ( $expr ) {

    if ( preg_match ( '/^\$([A-Za-z_][A-Za-z0-9_.:@]*)$/', trim ( $expr ), $match ) ) {
      if ( padArrayCheck ( $match [1] ) ) return padArrayValue ( $match [1] );
      if ( padFieldCheck ( $match [1] ) ) return padFieldValue ( $match [1] );
    }

    return padEval ( $expr );

  }

  function padJsonviewNamed ( $name ) {

    global $padDataStore, $pqStore;

    if ( ! is_string ( $name ) or ! padValidVar ( $name ) )
      return NULL;

    if ( isset ( $padDataStore [$name] ) ) return $padDataStore [$name];
    if ( isset ( $pqStore      [$name] ) ) return $pqStore      [$name];

    if ( isset ( $GLOBALS [$name] ) and is_array ( $GLOBALS [$name] ) and ! padStrHidden ( $name ) )
      return $GLOBALS [$name];

    $file = padDataFileName ( $name );

    if ( $file and str_ends_with ( $file, '.json' ) )
      return padJsonviewText ( file_get_contents ( $file ) );

    return $file ? padDataFileData ( $file ) : NULL;

  }

  function padJsonviewText ( $text ) {

    $text = trim ( (string) $text );

    if ( $text === '' )
      return NULL;

    try {
      return json_decode ( $text, TRUE, 512, JSON_THROW_ON_ERROR );
    } catch ( JsonException $e ) {
      return NULL;
    }

  }

  // Why a text is no JSON, in PHP's words, for the error.

  function padJsonviewError ( $text ) {

    json_decode ( trim ( (string) $text ) );

    return json_last_error_msg ();

  }

  function padJsonviewHtml ( $value, $open, $title ) {

    return padJsonviewStyle () . '<div class="pad-jsonview">'
         . padJsonviewNode ( $title !== '' ? $title : NULL, $value, 0, $open, FALSE ) . '</div>';

  }

  // $key is NULL for the root without a title - with one, the title - an int for an item of
  // a list, shown as its index, and a string for a key of an object, quoted as JSON writes
  // it.

  function padJsonviewNode ( $key, $value, $depth, $open, $inList ) {

    if ( is_object ( $value ) )
      $value = padToArray ( $value );

    if ( $key === NULL )
      $label = '';
    elseif ( $depth == 0 )
      $label = '<span class="pad-jsonview-title">' . padChartAttr ( $key ) . '</span> ';
    elseif ( $inList )
      $label = '<span class="pad-jsonview-index">' . (int) $key . '</span><span class="pad-jsonview-colon">:</span> ';
    else
      $label = '<span class="pad-jsonview-key">' . padChartAttr ( json_encode ( (string) $key, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) )
             . '</span><span class="pad-jsonview-colon">:</span> ';

    if ( ! is_array ( $value ) )
      return $label . padJsonviewScalar ( $value );

    $list = array_is_list ( $value );

    list ( $first, $last ) = $list ? [ '[', ']' ] : [ '{', '}' ];

    $count = count ( $value );

    if ( ! $count )
      return $label . "<span class=\"pad-jsonview-brace\">$first$last</span>";

    $what  = $list ? ( $count == 1 ? 'item' : 'items' ) : ( $count == 1 ? 'key' : 'keys' );
    $items = '';

    foreach ( $value as $one => $inner )
      $items .= '<li>' . padJsonviewNode ( $one, $inner, $depth + 1, $open, $list ) . '</li>';

    return '<details' . ( $depth < $open ? ' open' : '' ) . '><summary>' . $label
         . "<span class=\"pad-jsonview-brace\">$first</span>"
         . "<span class=\"pad-jsonview-fold\">…$last</span>"
         . " <span class=\"pad-jsonview-count\">$count $what</span></summary>"
         . "<ul>$items</ul><span class=\"pad-jsonview-brace\">$last</span></details>";

  }

  function padJsonviewScalar ( $value ) {

    if ( $value === NULL )
      return '<span class="pad-jsonview-null">null</span>';

    if ( is_bool ( $value ) )
      return '<span class="pad-jsonview-boolean">' . ( $value ? 'true' : 'false' ) . '</span>';

    if ( is_int ( $value ) or is_float ( $value ) )
      return '<span class="pad-jsonview-number">' . padChartAttr ( is_finite ( (float) $value ) ? json_encode ( $value ) : (string) $value ) . '</span>';

    return '<span class="pad-jsonview-string">'
         . padChartAttr ( json_encode ( (string) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE ) )
         . '</span>';

  }

  // Once per request: the rules hold for every tree after the first.

  function padJsonviewStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    $roles = [ 'key'     => [ '#7a3e9d', '#c8a2f0' ],
               'string'  => [ '#1a7f4b', '#7fd4a0' ],
               'number'  => [ '#1f5fbf', '#79b0f2' ],
               'boolean' => [ '#b35900', '#f0a860' ],
               'null'    => [ '#8a3b3b', '#e08a8a' ],
               'muted'   => [ '#77756f', '#96958d' ],
               'line'    => [ '#e4e3df', '#3a3a37' ],
               'surface' => [ '#fbfbfa', '#1b1b1a' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-jsonview-$role:$day;";
      $both  .= "--pad-jsonview-$role:light-dark($day,$night);";
    }

    return '<style>'
         . ":where(.pad-jsonview){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-jsonview){{$both}}}"
         . '.pad-jsonview{font:13px/1.6 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;background:var(--pad-jsonview-surface);'
         .   'border:1px solid var(--pad-jsonview-line);border-radius:8px;padding:10px 14px;overflow-x:auto}'
         . '.pad-jsonview ul{list-style:none;margin:0;padding:0 0 0 1.1em;border-left:1px dotted var(--pad-jsonview-line);margin-left:.3em}'
         . '.pad-jsonview li{padding-left:1em;white-space:nowrap}'
         . '.pad-jsonview li:has(>details){padding-left:0}'
         . '.pad-jsonview summary{cursor:pointer;list-style:none;border-radius:4px}'
         . '.pad-jsonview summary::-webkit-details-marker{display:none}'
         . '.pad-jsonview summary::before{content:"▸";display:inline-block;width:1em;color:var(--pad-jsonview-muted);transition:transform .15s}'
         . '.pad-jsonview details[open]>summary::before{transform:rotate(90deg)}'
         . '.pad-jsonview summary:focus-visible{outline:2px solid var(--pad-jsonview-number);outline-offset:1px}'
         . '.pad-jsonview details>.pad-jsonview-brace{padding-left:1em}'
         . '.pad-jsonview details[open]>summary .pad-jsonview-fold,.pad-jsonview details[open]>summary .pad-jsonview-count{display:none}'
         . '.pad-jsonview-key{color:var(--pad-jsonview-key)}'
         . '.pad-jsonview-string{color:var(--pad-jsonview-string);white-space:pre-wrap}'
         . '.pad-jsonview-number{color:var(--pad-jsonview-number)}'
         . '.pad-jsonview-boolean{color:var(--pad-jsonview-boolean);font-weight:600}'
         . '.pad-jsonview-null{color:var(--pad-jsonview-null);font-style:italic}'
         . '.pad-jsonview-index,.pad-jsonview-colon,.pad-jsonview-brace,.pad-jsonview-fold{color:var(--pad-jsonview-muted)}'
         . '.pad-jsonview-title{font-weight:600}'
         . '.pad-jsonview-count{color:var(--pad-jsonview-muted);font-size:.85em;font-style:italic}'
         . '</style>';

  }

?>

<?php

  // The widgets subsystem: interactive parts of a page that work without JavaScript and get
  // better with it - tabs, an accordion, a carousel, a modal, a copy box, a poll ...
  //
  //   {tabs}{tab 'Overview'}...{/tab}{tab 'Specs'}...{/tab}{/tabs}
  //   {widget:modal 'terms', title='Terms of sale'}...{/widget:modal}
  //   {widget 'toast', 'Saved', tone='success'/}
  //
  // A widget is a file of widgets/types/ - the tag code of the widget, which checks what it
  // is given and builds its markup - and the files there are the widgets there are
  // (padWidgetTypes, lib/widget.php). Its functions stand in _lib/<widget>.php, its style
  // and script in assets/<widget>.css and .js. widgets/widget.php is the body every form of
  // a widget runs. What the widgets share with the other tags that draw - their ids, the
  // colours as custom properties, the style and the script once per page with the CSP
  // nonce, the text of a file of assets/ (padWidgetAsset) - is lib/widget.php; what only
  // the widgets use is here.
  //
  // padWidgetText    a text option, read as the characters it holds
  // padWidgetName    a widget's name parameter checked - letters, digits, _ and -
  // padWidgetChoice  an option that is one of a few words
  // padWidgetRows    the rows a data= option names, NULL without one
  // padWidgetLink    a link a widget writes, a javascript: or data: one refused

  // A text a widget shows - a title, a label, a button - read as the characters it holds, so
  // a field inside a quoted option is the text of the field.

  function padWidgetText ( $option, $default = '' ) {

    $value = padTagParm ( $option, $default );

    return is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : $default;

  }

  // The name a widget is given as its first parameter - {drawer 'filters'} - which makes its
  // id: letters, digits, _ and -, at most 64. FALSE, named under the strict check, when it
  // is not.

  function padWidgetName ( $kind, $example ) {

    global $padParm, $padCheckSyntax;

    if ( is_string ( $padParm ) and preg_match ( '/^[A-Za-z0-9_-]{1,64}$/D', $padParm ) )
      return $padParm;

    if ( $padCheckSyntax )
      padError ( "a {" . $kind . "} needs a name of letters, digits, _ and -, like $example" );

    return FALSE;

  }

  // An option that is one of a few words, named under the strict check when it is another -
  // the default then.

  function padWidgetChoice ( $kind, $option, $default, $choices ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $value = is_scalar ( $value ) ? strtolower ( trim ( padParmText ( (string) $value ) ) ) : '';

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( "the {" . $kind . "} has no $option '" . padMakeSafe ( $value, 20 ) . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  // The rows a data= option names - a {data} store, a sequence store, the page's array, a
  // field holding a list or a _data file, as {chart} and the games read them - or NULL
  // without data=. The level got those rows as its data from the data option, and would
  // write the widget once per row: it gets its one default occurrence back.

  function padWidgetRows ( $kind ) {

    global $pad, $padPrm, $padData, $padDataStore, $pqStore, $padCheckSyntax;

    if ( ! isset ( $padPrm [$pad] ['data'] ) )
      return NULL;

    padTagParm ( 'data' );

    $rows = $padData [$pad];
    $name = $padPrm [$pad] ['data'];

    $padData [$pad] = padDefaultData ();

    if ( ! is_string ( $name ) or ! padValidVar ( $name ) )
      return $rows;

    if ( isset ( $padDataStore [$name] ) ) return $padDataStore [$name];
    if ( isset ( $pqStore      [$name] ) ) return $pqStore      [$name];

    if ( isset ( $GLOBALS [$name] ) and is_array ( $GLOBALS [$name] ) )
      return $GLOBALS [$name];

    if ( padFieldCheck ( $name ) and is_array ( $field = padFieldValue ( $name ) ) )
      return $field;

    if ( $file = padDataFileName ( $name ) )
      return padDataFileData ( $file );

    if ( $padCheckSyntax and ! padStrHidden ( $name ) )
      padError ( "there is no data named '$name' for the {" . $kind . "}" );

    return [];

  }

  // A link a widget writes from what it is given, escaped for its attribute - a page of the
  // application, a path, an address of the web or a mail address; one of another scheme -
  // javascript:, data: - is no link, and '' comes back: a row of data may hold what a visitor
  // typed. The scheme is read as a browser reads it, without the tabs, line breaks and
  // spaces it skips, and from a link that is markup - a line of rendered content - after
  // its character references: java&#9;script&#58; is javascript: to a browser.

  function padWidgetLink ( $link, $markup = FALSE ) {

    $link = trim ( (string) $link );

    if ( $markup )
      $link = html_entity_decode ( $link, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

    if ( preg_match ( '/^([a-z][a-z0-9+.-]*):/i', preg_replace ( '/[\x00-\x20]+/', '', $link ), $scheme )
         and ! in_array ( strtolower ( $scheme [1] ), [ 'http', 'https', 'mailto' ], TRUE ) )
      return '';

    return htmlspecialchars ( $link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>

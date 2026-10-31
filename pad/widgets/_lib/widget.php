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

?>

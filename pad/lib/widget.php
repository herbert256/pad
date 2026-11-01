<?php

  // What the widgets share - {tabs}, {accordion}, {modal}, {copy} and the rest of the widgets
  // subsystem in subs/widgets/ - with the other tags that draw and play, the charts, the games,
  // {video} and {chess} among them: an id of their own on the page, their colours as custom
  // properties, their CSS and script written once, the script with this request's CSP nonce.
  //
  // padWidgetTypes   the widgets: the files of subs/widgets/types/ (padKinds, lib/subsystem.php) -
  //                  a widget is added by adding its file there; the subsystem's own code is
  //                  in subs/widgets/, loaded when a page uses a widget
  // padWidgetType    whether a name is a widget
  // padWidgetId      a deterministic id: made from the widget's own content, so a widget
  //                  served from the fragment cache and one rendered next to it, or the
  //                  widgets of two nested passes, never share one - the same widget drawn
  //                  twice in a request gets a number behind the second (as padChartOpen):
  //                  -n2, which no item of the first - -1, -2 - can be
  // padWidgetOnce    TRUE the first time a key is asked for in its output - the CSS and
  //                  the script of a kind go out with the first widget of that kind only;
  //                  the tags that draw keep their style to one per output with it too
  // padWidgetColors  the custom properties of a class with their light-dark() defaults: a
  //                  page saying color-scheme: light dark gets the dark steps in dark mode,
  //                  a browser without light-dark() keeps the light ones
  // padWidgetStyle   a <style> with those properties and the rules, once per page
  // padWidgetScript  a <script> once per page, carrying the nonce when $padCsp names one
  // padWidgetAttr    a text escaped for an attribute or an element's content
  // padWidgetAsset   the style or the script of a widget, a file of subs/widgets/assets/
  //
  // What these write is protected (padProtect): their braces are CSS and JavaScript, never
  // tags of the page around them - a tag that hands its rendering back through $padContent
  // at the end walk has it spliced into the text its parent goes on to scan.

  function padWidgetTypes () {

    return padKinds ( 'widgets' );

  }

  function padWidgetType ( $name ) {

    return padKind ( 'widgets', $name );

  }

  // A number option of a tag that draws - size=, stroke= - or its default, named under the
  // strict check: '1.5em', 'small' and 'large' were drawn at the 8px minimum without a word,
  // where the same tags name every other option they cannot use. A number written with px,
  // the unit it is in - size='32px' - is that number: it was drawn at 32 before the check.

  function padWidgetNumber ( $tag, $option, $default ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );

    if ( is_string ( $value ) )
      $value = preg_replace ( '/\s*px\s*$/i', '', $value );

    if ( is_numeric ( $value ) and is_finite ( (float) $value ) )
      return $value + 0;

    if ( $padCheckSyntax )
      padError ( "the $tag has no usable $option '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 ) . "' - a number of pixels" );

    return $default;

  }

  function padWidgetId ( $kind, $seed ) {

    global $pad, $padFragment;

    static $drawn = [];

    // A widget made inside a {cache} section that is rendering - to be kept - is seeded with
    // the section's key as well: kept, it comes back on a later request beside a widget of
    // the same names made then, and the two had one id and one radio group - choosing a tab
    // in one emptied the other.

    for ( $i = $pad ?? -1; $i >= 0; $i-- )
      if ( is_array ( $padFragment [$i] ?? NULL ) and ! $padFragment [$i] ['hit'] ) {
        $seed = [ $seed, $padFragment [$i] ['key'] ];
        break;
      }

    $id = "pad-$kind-" . substr ( md5 ( serialize ( $seed ) ), 0, 8 );

    $drawn [$id] = ( $drawn [$id] ?? 0 ) + 1;

    return ( $drawn [$id] > 1 ) ? $id . '-n' . $drawn [$id] : $id;

  }

  // Once per output: the page, a {cache} section that is being kept - it comes back on
  // other pages, where nothing else wrote its CSS and script - a mail rendered during the
  // request ($padOnceScope, lib/mail.php) and, for a style, a {shadow} root ($padShadowScope,
  // tags/shadow.php). Remembered for the PHP process, a section
  // was kept without the style or the script an earlier widget of its page had written -
  // a video's play button did nothing elsewhere - and a mail took the page's style.

  function padWidgetOnce ( $key ) {

    global $pad, $padFragment, $padOnceScope, $padShadowScope;

    static $done = [];

    $scope = (string) ( $padOnceScope ?? '' );

    // A {shadow} root takes no style from the page: what a widget writes once - its style,
    // the rules of its slots - is written once in every shadow root as well; a script runs
    // for the whole document and stays once a page.

    if ( $padShadowScope and ! str_starts_with ( $key, 'script:' ) )
      $scope .= '|' . end ( $padShadowScope );

    for ( $i = $pad ?? -1; $i >= 0; $i-- )
      if ( is_array ( $padFragment [$i] ?? NULL ) and ! $padFragment [$i] ['hit'] ) {
        $scope .= '|' . $padFragment [$i] ['key'];
        break;
      }

    if ( isset ( $done [$scope] [$key] ) )
      return FALSE;

    $done [$scope] [$key] = TRUE;

    return TRUE;

  }

  function padWidgetColors ( $class, $roles ) {

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-$class-$role:$day;";
      $both  .= "--pad-$class-$role:light-dark($day,$night);";
    }

    return ":where(.pad-$class){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-$class){{$both}}}";

  }

  function padWidgetStyle ( $class, $roles, $rules ) {

    if ( ! padWidgetOnce ( "style:$class" ) )
      return '';

    return padProtect ( '<style' . padStyleNonce () . '>' . padWidgetColors ( $class, $roles ) . $rules . '</style>' );

  }

  function padWidgetScript ( $key, $script ) {

    global $padCsp;

    if ( ! padWidgetOnce ( "script:$key" ) )
      return '';

    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . htmlspecialchars ( padNonce (), ENT_QUOTES ) . '"' : '';

    return padProtect ( "<script$nonce>$script</script>" );

  }

  // The text of a file of subs/widgets/assets/, kept for the request. A style sheet there is
  // written a rule a line and sent as one line - the line breaks and the indent after them
  // taken out, which a rule of these never holds - as the rules were sent when they stood in
  // the PHP; a script is sent as it stands, without the line break that ends the file.

  function padWidgetAsset ( $file ) {

    static $memo = [];

    if ( isset ( $memo [$file] ) )
      return $memo [$file];

    $text = (string) padFileGet ( PAD . "subs/widgets/assets/$file" );

    return $memo [$file] = str_ends_with ( $file, '.css' )
      ? preg_replace ( '/\R[ \t]*/', '', trim ( $text ) )
      : rtrim ( $text, "\r\n" );

  }

  function padWidgetAttr ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>

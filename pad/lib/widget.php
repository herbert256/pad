<?php

  // What the interactive tags share - {tabs}, {accordion}, {carousel}, {modal}, {copy} and
  // {poll}: an id of their own on the page, their colours as custom properties, their CSS
  // and script written once, the script with this request's CSP nonce.
  //
  // padWidgetId      a deterministic id: made from the widget's own content, so a widget
  //                  served from the fragment cache and one rendered next to it, or the
  //                  widgets of two nested passes, never share one - the same widget drawn
  //                  twice in a request gets a number behind the second (as padChartOpen)
  // padWidgetOnce    TRUE the first time a key is asked for in this request - the CSS and
  //                  the script of a kind go out with the first widget of that kind only
  // padWidgetColors  the custom properties of a class with their light-dark() defaults: a
  //                  page saying color-scheme: light dark gets the dark steps in dark mode,
  //                  a browser without light-dark() keeps the light ones
  // padWidgetStyle   a <style> with those properties and the rules, once per page
  // padWidgetScript  a <script> once per page, carrying the nonce when $padCsp names one
  // padWidgetAttr    a text escaped for an attribute or an element's content
  //
  // What these write is protected (padProtect): their braces are CSS and JavaScript, never
  // tags of the page around them - a tag that hands its rendering back through $padContent
  // at the end walk has it spliced into the text its parent goes on to scan.

  function padWidgetId ( $kind, $seed ) {

    static $drawn = [];

    $id = "pad-$kind-" . substr ( md5 ( serialize ( $seed ) ), 0, 8 );

    $drawn [$id] = ( $drawn [$id] ?? 0 ) + 1;

    return ( $drawn [$id] > 1 ) ? $id . '-' . $drawn [$id] : $id;

  }

  function padWidgetOnce ( $key ) {

    static $done = [];

    if ( isset ( $done [$key] ) )
      return FALSE;

    $done [$key] = TRUE;

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

    return padProtect ( '<style>' . padWidgetColors ( $class, $roles ) . $rules . '</style>' );

  }

  function padWidgetScript ( $key, $script ) {

    global $padCsp;

    if ( ! padWidgetOnce ( "script:$key" ) )
      return '';

    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . htmlspecialchars ( padNonce (), ENT_QUOTES ) . '"' : '';

    return padProtect ( "<script$nonce>$script</script>" );

  }

  function padWidgetAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>

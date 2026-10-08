<?php

  // {source} - the files behind the example on show, coloured on the server by
  // lib/highlight.php, one tab each: the template, the page's PHP when it has one, the
  // component the browser runs - www/react/ under the page's own name - and the files the
  // example's entry in _data/examples.json adds ('_providers/topic.php', 'www:pad-react.js').
  // The tabs are radio buttons and CSS: the viewer itself needs no script.

  $reactSourcePage = $GLOBALS ['example'] ['page'] ?? $padPage;
  $reactSourceWww  = dirname ( APPS ) . "/www/$padApp/";

  $reactSourceList = [ "$reactSourcePage.pad", "$reactSourcePage.php", "www:$reactSourcePage.js" ];

  foreach ( $GLOBALS ['example'] ['files'] ?? [] as $reactSourceOne )
    $reactSourceList [] = $reactSourceOne;

  $reactSourceTabs = $reactSourcePanels = '';
  $reactSourceAt   = 0;

  foreach ( $reactSourceList as $reactSourceOne ) {

    $reactSourceInWww = str_starts_with ( $reactSourceOne, 'www:' );
    $reactSourceName  = $reactSourceInWww ? substr ( $reactSourceOne, 4 ) : $reactSourceOne;
    $reactSourceFile  = ( $reactSourceInWww ? $reactSourceWww : APP ) . $reactSourceName;
    $reactSourceShown = ( $reactSourceInWww ? "www/$padApp/" : '' ) . $reactSourceName;

    if ( ! is_file ( $reactSourceFile ) )
      continue;

    $reactSourceAt++;
    $reactSourceId = "source-$reactSourceAt";
    $reactSourceKind = $reactSourceInWww ? 'client' : 'server';

    $reactSourceTabs .= '<input type="radio" name="source" id="' . $reactSourceId . '"' . ( $reactSourceAt == 1 ? ' checked' : '' ) . '>'
                      . '<label for="' . $reactSourceId . '" class="is-' . $reactSourceKind . '">' . htmlspecialchars ( $reactSourceShown ) . '</label>';

    $reactSourcePanels .= '<div class="source-panel">'
                        . padHighlight ( file_get_contents ( $reactSourceFile ), pathinfo ( $reactSourceFile, PATHINFO_EXTENSION ), TRUE )
                        . '</div>';

  }

  return '<div class="source">' . $reactSourceTabs . $reactSourcePanels . '</div>';

?>

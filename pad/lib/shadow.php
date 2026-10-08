<?php

  // Declarative shadow DOM: a custom element rendered on the server with its shadow root
  // already in it, so it shows - encapsulated styles, slots and all - before any script ran,
  // and a script that upgrades it finds the root waiting.
  //
  //   <pad-card>
  //     {shadow css='www:card.css'}
  //       <header><slot name="title"></slot></header>
  //       <slot></slot>
  //     {/shadow}
  //     <h2 slot="title">{$title}</h2>
  //     <p>The light DOM, slotted by the browser.</p>
  //   </pad-card>
  //
  // {shadow} writes <template shadowrootmode="open"> round its content - mode='closed' for a
  // closed root, focus for shadowrootdelegatesfocus - and inlines the stylesheets css= names
  // as one <style> at the start of the root: a shadow root takes no styles from the page, and
  // a <style> written into a template would have its braces read as PAD tags. The files follow
  // the rule of {source}: a file of the application, or www: one in its www/ directory.
  //
  // padShadowOpen  the opening template tag with its <style>
  // padShadowCss   the text of the stylesheets, read once a request each

  function padShadowOpen ( $css, $mode, $focus ) {

    $mode = strtolower ( trim ( (string) $mode ) );

    if ( ! in_array ( $mode, [ 'open', 'closed' ], TRUE ) ) {
      padError ( "{shadow} has no mode '" . padMakeSafe ( $mode, 20 ) . "' - open or closed" );
      $mode = 'open';
    }

    $open = '<template shadowrootmode="' . $mode . '"' . ( $focus ? ' shadowrootdelegatesfocus' : '' ) . '>';
    $text = padShadowCss ( padSourceList ( $css ) );

    return $text === '' ? $open : $open . '<style>' . $text . '</style>';

  }

  function padShadowCss ( $files ) {

    static $read = [];

    $text = '';

    foreach ( $files as $name ) {

      if ( ! isset ( $read [$name] ) ) {

        $path = padSourcePath ( $name );

        if ( $path === FALSE or strtolower ( pathinfo ( $path, PATHINFO_EXTENSION ) ) !== 'css' ) {
          padError ( "{shadow} has no stylesheet '" . padMakeSafe ( $name, 60 ) . "' of the application - a .css file, www: for one in its www/ directory" );
          $read [$name] = '';
          continue;
        }

        // A </style> in the file would end the element early.

        $read [$name] = trim ( str_ireplace ( '</style', '<\/style', file_get_contents ( $path ) ) );

      }

      $text .= ( $text !== '' ? "\n" : '' ) . $read [$name];

    }

    return $text;

  }

?>

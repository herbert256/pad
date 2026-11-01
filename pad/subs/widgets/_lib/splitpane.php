<?php

  // Two panes and the bar between them - the splitpane widget, the WAI-ARIA window splitter
  // pattern: an editor beside its preview, a list beside the item it shows, an inspector.
  //
  //   {splitpane size=30, min=20, max=60, collapse}
  //     {tab 'Folders'}<ul>...</ul>{/tab}
  //     {tab 'Message'}<p>...</p>{/tab}
  //   {/splitpane}
  //
  // The first {tab} is the primary pane, whose size the bar sets - in percent of the whole,
  // so a window or a column that changes its width keeps the share - and the second takes
  // the rest. Each pane is a region named by its label; the bar is a separator that controls
  // the first pane, named by its label as well, its value the first pane's share: a screen
  // reader says 'Folders, separator, 30'. The bar takes the focus; the arrow keys move it by
  // 5, with Shift by 10 - left and right side by side (turned round in a right-to-left page),
  // up and down stacked - Home and End to min and max, and with collapse Enter folds the
  // first pane away and back, a double click as well. A drag moves it with any pointer -
  // mouse, pen or touch.
  //
  // Without scripting the bar is not there: the first pane has the browser's own resize grip
  // in its corner, between min and max - nothing to focus that does nothing. With scripting
  // a small script, once per page with the CSP nonce, moves the bar: the share is a custom
  // property it sets on the panes' box. After a move it sends a pad-splitpane event, which
  // bubbles, the share in its detail - keeping it is the application's.
  //
  // In a box narrower than 26rem the panes stand one above the other, each as high as its
  // content, and the bar goes: side by side there they had no room to read. size, min, max and the height are written for
  // the one splitpane in a <style> with the CSP nonce, by its id - no style attribute, which
  // a nonce policy refuses.
  //
  // padSplitpane        the box, the panes and the bar around the rendered {tab}s
  // padSplitpaneShare   a size=, min= or max= - a whole percentage from 0 to 100
  // padSplitpaneHeight  the height= - a CSS length, '' for none
  // padSplitpaneStyle   the CSS, www/assets/widgets/splitpane.css, once per page
  // padSplitpaneScript  the script, www/assets/widgets/splitpane.js, once per page

  include_once PAD . 'subs/widgets/_lib/tabs.php';

  function padSplitpane ( $items, $size, $min, $max, $stacked, $collapse, $height ) {

    $id     = padWidgetId ( 'splitpane', [ array_column ( $items, 'label' ), $size, $stacked ] );
    $first  = padWidgetAttr ( $items [0] ['label'] );
    $second = padWidgetAttr ( $items [1] ['label'] );
    $shape  = $stacked ? 'stacked' : 'row';
    $turn   = $stacked ? 'horizontal' : 'vertical';
    $low    = $collapse ? 0 : $min;
    $fold   = $collapse ? ' data-pad-collapse' : '';

    $own = '';

    foreach ( [ 'size' => [ $size, 50 ], 'min' => [ $min, 10 ], 'max' => [ $max, 90 ] ] as $name => [ $value, $default ] )
      if ( $value != $default )
        $own .= "--pad-splitpane-$name:$value%;";

    if ( $height !== '' )
      $own .= "--pad-splitpane-height:$height;";

    return padSplitpaneStyle ()
         . ( $own !== '' ? padProtect ( '<style' . padStyleNonce () . ">#$id{{$own}}</style>" ) : '' )
         . padProtect ( "<div class=\"pad-splitpane pad-splitpane-$shape\" id=\"$id\">"
                      . "<div class=\"pad-splitpane-box\" data-pad-min=\"$min\" data-pad-max=\"$max\"$fold>"
                      . "<div class=\"pad-splitpane-pane pad-splitpane-first\" id=\"$id-1\" role=\"region\" aria-label=\"$first\">" )
         . $items [0] ['content']
         . padProtect ( '</div>'
                      . "<div class=\"pad-splitpane-bar\" role=\"separator\" tabindex=\"0\" aria-controls=\"$id-1\" aria-label=\"$first\""
                      . " aria-orientation=\"$turn\" aria-valuenow=\"$size\" aria-valuemin=\"$low\" aria-valuemax=\"$max\"></div>"
                      . "<div class=\"pad-splitpane-pane pad-splitpane-second\" id=\"$id-2\" role=\"region\" aria-label=\"$second\">" )
         . $items [1] ['content']
         . padProtect ( '</div></div></div>' )
         . padSplitpaneScript ();

  }

  // A share in percent - size=30, min='20' - a whole number from 0 to 100, its default
  // named under the strict check when it is another.

  function padSplitpaneShare ( $option, $default ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $text  = is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : '';
    $text  = preg_replace ( '/\s*%$/', '', $text );

    if ( preg_match ( '/^\d{1,3}$/D', $text ) and (int) $text <= 100 )
      return (int) $text;

    if ( $padCheckSyntax )
      padError ( "the {splitpane} has no usable $option '" . padMakeSafe ( $text, 20 ) . "' - a whole percentage from 0 to 100" );

    return $default;

  }

  // The height of the panes - a CSS length of px, rem, em or the window's height - '' for
  // none: side by side the panes are then as high as their content, stacked 20rem.

  function padSplitpaneHeight () {

    global $padCheckSyntax;

    $value = padTagParm ( 'height', '' );
    $text  = is_scalar ( $value ) ? strtolower ( trim ( padParmText ( (string) $value ) ) ) : '';

    if ( $text === '' or preg_match ( '/^\d{1,4}(\.\d{1,3})?(px|rem|em|vh|svh|lvh|dvh)$/D', $text ) )
      return $text;

    if ( preg_match ( '/^\d{1,4}$/D', $text ) )
      return $text . 'px';

    if ( $padCheckSyntax )
      padError ( "the {splitpane} has no usable height '" . padMakeSafe ( $text, 20 ) . "' - a length like 300px, 24rem or 60vh" );

    return '';

  }

  function padSplitpaneStyle () {

    return padWidgetStyle ( 'splitpane',
      [ 'accent'  => [ 'var(--accent,#2a78d6)', 'var(--accent,#5598e7)' ],
        'text'    => [ 'var(--text,#1f1f1d)', 'var(--text,#ecebe6)' ],
        'muted'   => [ 'var(--dim,#62615c)', 'var(--dim,#a9a8a0)' ],
        'line'    => [ 'var(--line,#e4e3df)', 'var(--line,#3a3a37)' ],
        'surface' => [ 'var(--panel,#ffffff)', 'var(--panel,#1f1f1d)' ] ],
      padWidgetAsset ( 'splitpane.css' ) );

  }

  function padSplitpaneScript () {

    return padWidgetScript ( 'splitpane', padWidgetAsset ( 'splitpane.js' ) );

  }

?>

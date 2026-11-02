<?php

  // A menu of the actions on a part of the page - the contextmenu widget: a right click on
  // the part opens it at the pointer, the context menu key or Shift+F10 at the focused
  // element, and a small button beside it opens the same menu for whoever looks for one.
  //
  //   <li>report.pdf
  //     {widget:contextmenu 'Actions for report.pdf'}
  //       <a href="?open&file=report.pdf">Open</a>
  //       <a href="?rename&file=report.pdf">Rename</a>
  //       <hr>
  //       <button type="submit" form="delete-report">Delete</button>
  //     {/widget:contextmenu}
  //   </li>
  //
  // The part is the element the menu stands in - the <li> above - or, with area=, the
  // nearest element around it that the selector names: area='tr' makes the whole row the
  // part when the menu stands in one of its cells. The first parameter names the menu and
  // its button for a screen reader.
  //
  // The button and the menu stand as the dropdown's do (subs/widgets/_lib/dropdown.php): the
  // menu a popover (the HTML popover attribute) the button opens through popovertarget, so
  // without a script the button opens it, the browser closes it on a click outside it and on
  // Escape, and places it under the button by CSS anchor positioning - the actions are never
  // reached by a right click alone, which no keyboard and no touch screen makes. The button
  // shows three dots; the name it has is the first parameter.
  //
  // A small script, once per page with the CSP nonce, adds the context menu itself - the
  // WAI-ARIA menu pattern: a right click in the part opens the menu at the pointer, kept
  // inside the window, and the context menu key or Shift+F10 opens it at the focused element;
  // the menu is a menu of menu items then, the focus on its first, the arrow keys, Home and
  // End move between them, a letter to the next item starting with it, Enter or a click runs
  // one and closes the menu, Escape and Tab close it and the focus goes back where it was. A
  // disabled item - <button disabled>, <a aria-disabled="true"> - is drawn faint and skipped,
  // and such a link loses its href, so it leads nowhere without a script either.
  // A right click with Shift, and one in a field, a text area or an editable part, keeps the
  // browser's own menu. Of menus whose parts lie in each other the innermost opens.
  //
  // padContextmenu        the button and the menu around the rendered content
  // padContextmenuArea    the area= - a tag name or a class, '' for the element it stands in
  // padContextmenuStyle   the CSS, www/assets/widgets/contextmenu.css, once per page
  // padContextmenuScript  the script, www/assets/widgets/contextmenu.js, once per page

  function padContextmenu ( $name, $items, $area ) {

    $id   = padWidgetId ( 'contextmenu', [ $name, $items, $area ] );
    $name = padWidgetAttr ( $name );
    $part = ( $area !== '' ) ? ' data-pad-area="' . padWidgetAttr ( $area ) . '"' : '';

    return padContextmenuStyle ()
         . padProtect ( "<div class=\"pad-contextmenu\" id=\"$id\"$part>"
                      . "<button type=\"button\" class=\"pad-contextmenu-button\" popovertarget=\"$id-menu\" aria-label=\"$name\">"
                      . '<span aria-hidden="true">&#8943;</span></button>'
                      . "<div class=\"pad-contextmenu-menu\" id=\"$id-menu\" popover>" )
         . padContextmenuDisabled ( $items )
         . padProtect ( '</div></div>' )
         . padContextmenuScript ();

  }

  // A link of the menu marked aria-disabled="true" loses its href: drawn faint and skipped,
  // it leads nowhere without a script too, where the button opens the menu and only the
  // script stopped a click on it or Enter - <a href="?delete" aria-disabled="true"> was
  // followed. The value is matched as the script and the CSS match it, exactly true.

  function padContextmenuDisabled ( $items ) {

    return preg_replace_callback ( '/<a\b(?:[^>"\']++|"[^"]*+"|\'[^\']*+\')*+>/i', function ( $tag ) {

      if ( ! preg_match ( '/\s(?i:aria-disabled)\s*=\s*(?:"true"|\'true\'|true(?=[\s\/>]))/', $tag [0] ) )
        return $tag [0];

      return preg_replace ( '/\shref\s*=\s*(?:"[^"]*+"|\'[^\']*+\'|[^\s"\'>]++)/i', '', $tag [0] );

    }, $items );

  }

  // The part a menu belongs to, area=: a tag name, a class or the two - tr, .card, li.file -
  // which the script hands to closest (); '' for the element the menu stands in. Another
  // selector is named under the strict check.

  function padContextmenuArea () {

    global $padCheckSyntax;

    $value = padTagParm ( 'area', '' );
    $text  = is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : '';

    if ( $text === '' or preg_match ( '/^(?:[a-zA-Z][a-zA-Z0-9-]*)?(?:\.[a-zA-Z_][a-zA-Z0-9_-]*)*$/D', $text ) )
      return $text;

    if ( $padCheckSyntax )
      padError ( "the {widget:contextmenu} has no usable area '" . padMakeSafe ( $text, 30 ) . "' - a tag name or a class, like tr, li or .card" );

    return '';

  }

  function padContextmenuStyle () {

    return padWidgetStyle ( 'contextmenu',
      [ 'accent'  => [ 'var(--accent,#2a78d6)', 'var(--accent,#5598e7)' ],
        'text'    => [ 'var(--text,#1f1f1d)', 'var(--text,#ecebe6)' ],
        'muted'   => [ 'var(--dim,#62615c)', 'var(--dim,#a9a8a0)' ],
        'line'    => [ 'var(--line,#e4e3df)', 'var(--line,#3a3a37)' ],
        'surface' => [ 'var(--panel,#ffffff)', 'var(--panel,#242422)' ],
        'hover'   => [ 'var(--accent-soft,#f1f0ec)', 'var(--accent-soft,#30302d)' ] ],
      padWidgetAsset ( 'contextmenu.css' ) );

  }

  function padContextmenuScript () {

    return padWidgetScript ( 'contextmenu', padWidgetAsset ( 'contextmenu.js' ) );

  }

?>

<?php

  // A menu under a button - the dropdown widget.
  //
  //   {dropdown 'Account', align='end'}
  //     <a href="?profile">Profile</a>
  //     <a href="?settings">Settings</a>
  //     <hr>
  //     <a href="?logout">Log out</a>
  //   {/dropdown}
  //
  // The menu is a popover (the HTML popover attribute) that the button opens through
  // popovertarget: the browser shows it in the top layer - no overflow of a parent cuts it
  // off - closes it on a click outside it, on Escape and when another one opens, gives the
  // button its 'expanded' state for a screen reader, and puts the menu next in the tab order
  // after the button. None of that needs a script. The menu stands under its button by CSS
  // anchor positioning - the button that opens a popover is its anchor - flipped above it
  // where there is no room below.
  //
  // A small script, once per page with the CSP nonce, adds what the browser does not: the
  // arrow keys, Home and End move between the items, the down arrow on the button opens the
  // menu at its first item, a click on an item closes it, and a browser without anchor
  // positioning gets the menu placed under the button by the script - without either it
  // shows in the middle of the window, as a popover does. The menu takes any markup; links,
  // buttons, a rule (<hr>) and a line of text (<p>) are drawn as a menu's.
  //
  // padDropdown        the button and the menu around the rendered content
  // padDropdownStyle   the CSS, www/assets/widgets/dropdown.css, once per page
  // padDropdownScript  the script, www/assets/widgets/dropdown.js, once per page

  function padDropdown ( $label, $items, $align ) {

    $id = padWidgetId ( 'dropdown', [ $label, $items, $align ] );

    return padDropdownStyle ()
         . padProtect ( "<div class=\"pad-dropdown\" id=\"$id\">"
                      . "<button type=\"button\" class=\"pad-dropdown-button\" popovertarget=\"$id-menu\">" . padWidgetAttr ( $label )
                      . '<span class="pad-dropdown-caret" aria-hidden="true"></span></button>'
                      . "<div class=\"pad-dropdown-menu pad-dropdown-$align\" id=\"$id-menu\" popover>" )
         . $items
         . padProtect ( '</div></div>' )
         . padDropdownScript ();

  }

  function padDropdownStyle () {

    return padWidgetStyle ( 'dropdown',
      [ 'accent'  => [ '#2a78d6', '#5598e7' ],
        'text'    => [ '#1f1f1d', '#ecebe6' ],
        'muted'   => [ '#62615c', '#a9a8a0' ],
        'line'    => [ '#e4e3df', '#3a3a37' ],
        'surface' => [ '#ffffff', '#242422' ],
        'hover'   => [ '#f1f0ec', '#30302d' ] ],
      padWidgetAsset ( 'dropdown.css' ) );

  }

  function padDropdownScript () {

    return padWidgetScript ( 'dropdown', padWidgetAsset ( 'dropdown.js' ) );

  }

?>

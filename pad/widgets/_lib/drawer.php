<?php

  // A panel that slides in from the side - the drawer widget: an HTML <dialog> along an edge
  // of the window and the button that opens it.
  //
  //   {drawer 'filters', title='Filters', button='Show filters', side='left'}
  //     {form 'filter'}...{/form}
  //   {/drawer}
  //
  // It is opened the way {modal} opens its dialog (widgets/_lib/modal.php): the button's
  // invoker commands where the browser has them; a small script, once per page with the CSP
  // nonce, where it has not; and with scripting off a link to the panel's id, which the CSS
  // shows as an overlay (:target) with a close link back to the opener - @media (scripting)
  // decides between the button and the links. Opened, the page behind is inert, the focus
  // moves into the panel and back to the button afterwards, and Escape, the close button or
  // a click on the backdrop closes it. An address ending in the panel's id opens it.
  //
  // The button, its link and the panel stand in one <div class="pad-drawer"> that takes no
  // room of its own (display: contents): the custom properties of the colours are set on it,
  // so the button, which stands outside the panel, has them too.
  //
  // side= is the edge: right (the default) and left, the whole height of the window, or
  // bottom, a sheet across its width - the panel slides in from there unless reduced motion
  // is asked for. The id is the drawer's name, pad-drawer-filters. What the drawer adds to it
  // - the number of a second drawer of one name, pad-drawer-filters.2, and the ids of its
  // opening link and its title, .open and .title - follows a dot, which no name can hold: a
  // dash, as before, made the second 'f' and a drawer named 'f-2' one id, and the link of
  // 'g' and the panel of a drawer named 'g-open' - the button of one opened the other.
  //
  // padDrawer        the button, the links and the panel around the rendered content
  // padDrawerStyle   the CSS, assets/drawer.css, once per page
  // padDrawerScript  the fallback for a browser without invoker commands, assets/drawer.js,
  //                  once per page

  function padDrawer ( $name, $title, $button, $side, $content ) {

    static $names = [];

    $names [$name] = ( $names [$name] ?? 0 ) + 1;

    $id     = "pad-drawer-$name" . ( $names [$name] > 1 ? '.' . $names [$name] : '' );
    $title  = padWidgetAttr ( $title );
    $button = padWidgetAttr ( $button );

    return padDrawerStyle ()
         . padProtect ( '<div class="pad-drawer">'
                      . "<button type=\"button\" class=\"pad-drawer-button\" commandfor=\"$id\" command=\"show-modal\">$button</button>"
                      . "<a class=\"pad-drawer-button pad-drawer-link\" id=\"$id.open\" href=\"#$id\">$button</a>"
                      . "<dialog class=\"pad-drawer-panel pad-drawer-$side\" id=\"$id\" aria-labelledby=\"$id.title\" closedby=\"any\">"
                      . '<div class="pad-drawer-head">'
                      . "<h2 class=\"pad-drawer-title\" id=\"$id.title\">$title</h2>"
                      . "<button type=\"button\" class=\"pad-drawer-close\" commandfor=\"$id\" command=\"close\" aria-label=\"Close\"><span aria-hidden=\"true\">&#215;</span></button>"
                      . "<a class=\"pad-drawer-close pad-drawer-link\" href=\"#$id.open\" aria-label=\"Close\"><span aria-hidden=\"true\">&#215;</span></a>"
                      . '</div><div class="pad-drawer-body">' )
         . $content
         . padProtect ( '</div></dialog></div>' )
         . padDrawerScript ();

  }

  function padDrawerStyle () {

    return padWidgetStyle ( 'drawer',
      [ 'accent'   => [ '#2a78d6', '#5598e7' ],
        'on'       => [ '#ffffff', '#0d1b2e' ],
        'text'     => [ '#1f1f1d', '#ecebe6' ],
        'muted'    => [ '#62615c', '#a9a8a0' ],
        'line'     => [ '#e4e3df', '#3a3a37' ],
        'surface'  => [ '#ffffff', '#1f1f1d' ],
        'backdrop' => [ 'rgba(20,20,18,.45)', 'rgba(0,0,0,.65)' ] ],
      padWidgetAsset ( 'drawer.css' ) );

  }

  function padDrawerScript () {

    return padWidgetScript ( 'drawer', padWidgetAsset ( 'drawer.js' ) );

  }

?>

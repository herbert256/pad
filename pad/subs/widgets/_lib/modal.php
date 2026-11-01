<?php

  // A modal dialog - the modal widget: an HTML <dialog> and the button that opens it.
  //
  //   {modal 'terms', title='Terms of sale', button='Read the terms'}
  //     <p>...</p>
  //   {/modal}
  //
  // It is opened in the first of three ways the browser can:
  //
  //   - the button's commandfor and command="show-modal" attributes (HTML invoker
  //     commands): the browser opens the dialog itself, no script runs;
  //   - a browser without them has a small script, written once per page with the CSP
  //     nonce, call showModal () and close () for the same buttons;
  //   - with scripting off, a link to the dialog's id stands in for the button, and the
  //     CSS shows the targeted dialog as an overlay (:target), its close link going back to
  //     the opener. The button and the links are told apart by @media (scripting): a browser
  //     too old to know that query shows the links, which work everywhere.
  //
  // Opened as a modal the dialog is what the browser makes of one: the page behind inert,
  // the focus moved in and back to the button on closing, Escape and a click on the backdrop
  // (closedby="any", and the script for a browser without it) close it. The title names the
  // dialog (aria-labelledby); an address ending in the dialog's id opens it on arrival, and
  // so does a link to it on the page. With scripting on, the no-script overlay of a targeted
  // dialog stays off: it showed the dialog again once it was closed, now no dialog the close
  // button or Escape reached - a dialog opened by its address could not be closed.
  //
  // The id is the modal's name - pad-modal-terms - so a link elsewhere can open it too: ?page#pad-modal-terms.
  // What the modal adds to it - the number of a second modal of one name, pad-modal-terms.2,
  // and the ids of its opening link and its title, .open and .title - follows a dot, which no
  // name can hold: a dash, as before, made the second 'terms' and a modal named 'terms-2'
  // one id, and the link of 'terms' and the dialog of a modal named 'terms-open'.
  //
  // padModal        the button, the links and the dialog around the rendered content
  // padModalStyle   the CSS, assets/modal.css, once per page
  // padModalScript  the fallback for a browser without invoker commands, assets/modal.js,
  //                 once per page

  function padModal ( $name, $title, $button, $content ) {

    static $names = [];

    $names [$name] = ( $names [$name] ?? 0 ) + 1;

    $id     = "pad-modal-$name" . ( $names [$name] > 1 ? '.' . $names [$name] : '' );
    $title  = padWidgetAttr ( $title );
    $button = padWidgetAttr ( $button );

    return padModalStyle ()
         . padProtect ( "<button type=\"button\" class=\"pad-modal-button\" commandfor=\"$id\" command=\"show-modal\">$button</button>"
                      . "<a class=\"pad-modal-button pad-modal-link\" id=\"$id.open\" href=\"#$id\">$button</a>"
                      . "<dialog class=\"pad-modal\" id=\"$id\" aria-labelledby=\"$id.title\" closedby=\"any\">"
                      . '<div class="pad-modal-frame"><div class="pad-modal-head">'
                      . "<h2 class=\"pad-modal-title\" id=\"$id.title\">$title</h2>"
                      . "<button type=\"button\" class=\"pad-modal-close\" commandfor=\"$id\" command=\"close\" aria-label=\"Close\"><span aria-hidden=\"true\">&#215;</span></button>"
                      . "<a class=\"pad-modal-close pad-modal-link\" href=\"#$id.open\" aria-label=\"Close\"><span aria-hidden=\"true\">&#215;</span></a>"
                      . '</div><div class="pad-modal-body">' )
         . $content
         . padProtect ( '</div></div></dialog>' )
         . padModalScript ();

  }

  function padModalStyle () {

    return padWidgetStyle ( 'modal',
      [ 'accent'   => [ '#2a78d6', '#5598e7' ],
        'on'       => [ '#ffffff', '#0d1b2e' ],
        'text'     => [ '#1f1f1d', '#ecebe6' ],
        'muted'    => [ '#62615c', '#a9a8a0' ],
        'line'     => [ '#e4e3df', '#3a3a37' ],
        'surface'  => [ '#ffffff', '#1f1f1d' ],
        'backdrop' => [ 'rgba(20,20,18,.45)', 'rgba(0,0,0,.65)' ] ],
      padWidgetAsset ( 'modal.css' ) );

  }

  // The script only acts in a browser without invoker commands, and for an address that
  // names a dialog. A click whose target is the dialog itself fell on the backdrop: the
  // frame inside covers the whole box. A dialog that closes takes its id off the address:
  // the address kept it, and a second click on a link to the dialog changed nothing, so no
  // hashchange came and the dialog stayed shut.

  function padModalScript () {

    return padWidgetScript ( 'modal', padWidgetAsset ( 'modal.js' ) );

  }

?>

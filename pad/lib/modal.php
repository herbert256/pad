<?php

  // A modal dialog - the {modal} tag: an HTML <dialog> and the button that opens it.
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
  // dialog (aria-labelledby); an address ending in the dialog's id opens it on arrival.
  //
  // The id is the modal's name - pad-modal-terms - so a link elsewhere can open it too: ?page#pad-modal-terms.
  //
  // padModal        the button, the links and the dialog around the rendered content
  // padModalStyle   the CSS, once per page
  // padModalScript  the fallback for a browser without invoker commands, once per page

  function padModal ( $name, $title, $button, $content ) {

    static $names = [];

    $names [$name] = ( $names [$name] ?? 0 ) + 1;

    $id     = "pad-modal-$name" . ( $names [$name] > 1 ? '-' . $names [$name] : '' );
    $title  = padWidgetAttr ( $title );
    $button = padWidgetAttr ( $button );

    return padModalStyle ()
         . padProtect ( "<button type=\"button\" class=\"pad-modal-button\" commandfor=\"$id\" command=\"show-modal\">$button</button>"
                      . "<a class=\"pad-modal-button pad-modal-link\" id=\"$id-open\" href=\"#$id\">$button</a>"
                      . "<dialog class=\"pad-modal\" id=\"$id\" aria-labelledby=\"$id-title\" closedby=\"any\">"
                      . '<div class="pad-modal-frame"><div class="pad-modal-head">'
                      . "<h2 class=\"pad-modal-title\" id=\"$id-title\">$title</h2>"
                      . "<button type=\"button\" class=\"pad-modal-close\" commandfor=\"$id\" command=\"close\" aria-label=\"Close\"><span aria-hidden=\"true\">&#215;</span></button>"
                      . "<a class=\"pad-modal-close pad-modal-link\" href=\"#$id-open\" aria-label=\"Close\"><span aria-hidden=\"true\">&#215;</span></a>"
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
      '.pad-modal-button{display:inline-flex;align-items:center;gap:.4em;padding:.55em 1.1em;border:0;border-radius:8px;'
      .   'background:var(--pad-modal-accent);color:var(--pad-modal-on);font:inherit;font-weight:600;text-decoration:none;cursor:pointer}'
      . '.pad-modal-button:hover{filter:brightness(1.08)}'
      . '.pad-modal-button:focus-visible,.pad-modal-close:focus-visible{outline:2px solid var(--pad-modal-accent);outline-offset:2px}'
      . 'button.pad-modal-button,button.pad-modal-close{display:none}'
      . '@media (scripting:enabled){button.pad-modal-button{display:inline-flex}button.pad-modal-close{display:flex}'
      .   'a.pad-modal-link{display:none}}'
      . '.pad-modal{width:min(36rem,calc(100vw - 2rem));max-height:calc(100vh - 4rem);padding:0;border:1px solid var(--pad-modal-line);'
      .   'border-radius:14px;background:var(--pad-modal-surface);color:var(--pad-modal-text);'
      .   'box-shadow:0 20px 50px rgba(0,0,0,.3)}'
      . '.pad-modal::backdrop{background:rgba(20,20,18,.5)}'
      . '.pad-modal:target:not([open]){display:block;position:fixed;inset:0;margin:auto;height:fit-content;z-index:2147483000;'
      .   'overflow:auto;box-shadow:0 0 0 100vmax var(--pad-modal-backdrop)}'
      . '.pad-modal-frame{padding:1.2em 1.4em 1.4em}'
      . '.pad-modal-head{display:flex;align-items:flex-start;gap:1em;margin-bottom:.6em}'
      . '.pad-modal-title{flex:1;margin:0;font-size:1.2em;line-height:1.3}'
      . '.pad-modal-close{display:flex;align-items:center;justify-content:center;flex:none;width:2em;height:2em;'
      .   'margin:-.3em -.5em 0 0;padding:0;border:0;border-radius:50%;background:transparent;color:var(--pad-modal-muted);'
      .   'font:inherit;font-size:1.3em;line-height:1;text-decoration:none;cursor:pointer}'
      . '.pad-modal-close:hover{background:var(--pad-modal-line);color:var(--pad-modal-text)}'
      . '.pad-modal-body>:first-child{margin-top:0}.pad-modal-body>:last-child{margin-bottom:0}' );

  }

  // The script only acts in a browser without invoker commands, and for an address that
  // names a dialog. A click whose target is the dialog itself fell on the backdrop: the
  // frame inside covers the whole box.

  function padModalScript () {

    return padWidgetScript ( 'modal', <<<'SCRIPT'
(function () {
  if (window.padModalReady) return;
  window.padModalReady = true;
  var native = 'command' in HTMLButtonElement.prototype;
  document.addEventListener('click', function (e) {
    var target = e.target;
    if (target.matches && target.matches('dialog.pad-modal[open]')) { target.close(); return; }
    var button = target.closest && target.closest('button.pad-modal-button, button.pad-modal-close');
    if (!button || native) return;
    var dialog = document.getElementById(button.getAttribute('commandfor'));
    if (!dialog) return;
    if (button.getAttribute('command') === 'close') dialog.close();
    else if (!dialog.open) dialog.showModal();
  });
  function arrived() {
    var id = '';
    try { id = decodeURIComponent(location.hash.slice(1)); } catch (error) { return; }
    var dialog = id && document.getElementById(id);
    if (dialog && dialog.matches('dialog.pad-modal') && !dialog.open) dialog.showModal();
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', arrived);
  else arrived();
})();
SCRIPT );

  }

?>

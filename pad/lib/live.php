<?php

  // Live regions: a part of a page that answers clicks and forms by re-rendering on the
  // server and swapping itself in, without JavaScript of the application's own.
  //
  //   {live 'cart'}
  //     {cart}<li>{$item} x {$qty}</li>{/cart}
  //     <button pad-click="add" pad-value="42">Add</button>
  //   {/live}
  //
  // {live} wraps what it renders in <div data-pad-live="cart">, and the first region of a
  // page brings a small script along. The script listens on the whole document, so content a
  // region swaps in works the same: a click on a pad-click element, a submit of a pad-submit
  // form or a change of a pad-change field inside a region posts to the page's own URL -
  // padLive (the region), padEvent (the attribute's value) and padValue (pad-value, or the
  // field's value), plus the form's fields - and puts what comes back into the region.
  //
  // On the server that post is an ordinary request of the same page: its PHP runs, reads the
  // event with padLiveEvent () and padLiveValue (), and keeps state where a page keeps it - in
  // the session ($padSessionVars), a database, or the value the event carries. The page is
  // rendered as always, and exits/exits.php answers with the inner content of that one region
  // instead of the page; a page with no such region is an error.
  //
  // What a region rendered is kept in a static of padLiveStore, because a region inside a
  // nested pass - a {page}, a sandbox - would otherwise be undone with that pass's globals.
  //
  // padLive        the region this request re-renders, '' for an ordinary request
  // padLiveEvent   the event a live request carries
  // padLiveValue   its value
  // padLiveName    the region's name, checked
  // padLiveWrap    a rendered region wrapped, and kept for a live request
  // padLiveAnswer  the answer to a live request: the region's inner content
  // padLiveScript  the client side

  function padLive () {

    return padLiveField ( 'padLive' );

  }

  function padLiveEvent () {

    return padLiveField ( 'padEvent' );

  }

  function padLiveValue () {

    return (string) ( $_POST ['padValue'] ?? '' );

  }

  // A region and an event are names - letters, digits, _ and - - and a post carrying
  // anything else is no live request at all.

  function padLiveField ( $name ) {

    $value = (string) ( $_POST [$name] ?? '' );

    return preg_match ( '/^[A-Za-z0-9_-]{1,64}$/D', $value ) ? $value : '';

  }

  function &padLiveStore () {

    static $store = [ 'script' => FALSE, 'regions' => [] ];

    return $store;

  }

  // The name a {live} is given: letters, digits, _ and -, as the event a post may carry.

  function padLiveName ( $name ) {

    if ( ! preg_match ( '/^[A-Za-z0-9_-]{1,64}$/D', (string) $name ) )
      padError ( "a {live} region needs a name of letters, digits, _ and -, like {live 'cart'}" );

    return (string) $name;

  }

  // The rendered content as a region: kept for a live request that asks for it - the first
  // region of a name counts - and wrapped, the script behind the page's first region. The
  // script is protected, its braces are no tags of the page around it.

  function padLiveWrap ( $name, $content ) {

    $store = &padLiveStore ();

    if ( ! array_key_exists ( $name, $store ['regions'] ) )
      $store ['regions'] [$name] = $content;

    $script = '';

    if ( ! $store ['script'] ) {
      $store ['script'] = TRUE;
      $script = padProtect ( '<script>' . padLiveScript () . '</script>' );
    }

    // With $padCsrf on every post must bring the session's token back (lib/csrf.php), and
    // the post of a click has no form to carry it: the region holds the token, and the
    // script sends it along. A region without it was answered 403 on every event.

    $csrf = ( $GLOBALS ['padCsrf'] ?? FALSE ) ? ' data-pad-csrf="' . padCsrfToken () . '"' : '';

    return '<div data-pad-live="' . htmlspecialchars ( $name ) . '"' . $csrf . '>' . $content . '</div>' . $script;

  }

  function padLiveAnswer () {

    global $padMyTidy, $padTidy;

    $name  = padLive ();
    $store = &padLiveStore ();

    if ( ! array_key_exists ( $name, $store ['regions'] ) )
      return padError ( "there is no live region '$name' on this page" );

    $padTidy = $padMyTidy = FALSE;

    // The {stack} markers in the region are filled as exits/exits.php fills the page's -
    // the answer carried the bare marker instead of what was pushed.

    return padUnprotect ( padUnescape ( padStackFill ( $store ['regions'] [$name] ) ) );

  }

  function padLiveScript () {

    return <<<'SCRIPT'
(function () {
  if (window.padLiveReady) return;
  window.padLiveReady = true;
  function send(region, fields) {
    var body = new URLSearchParams();
    fields.forEach(function (field) { body.append(field[0], field[1]); });
    body.set('padLive', region.getAttribute('data-pad-live'));
    var token = region.getAttribute('data-pad-csrf');
    if (token && !body.has('padCsrfToken')) body.set('padCsrfToken', token);
    region.setAttribute('aria-busy', 'true');
    fetch(location.href, { method: 'POST', body: body, credentials: 'same-origin' })
      .then(function (response) {
        return response.text().then(function (text) {
          if (!response.ok) throw new Error(response.status + ' ' + response.statusText);
          return text;
        });
      })
      .then(function (html) { region.innerHTML = html; region.removeAttribute('data-pad-live-error'); })
      .catch(function (error) { region.setAttribute('data-pad-live-error', error.message); })
      .then(function () { region.removeAttribute('aria-busy'); });
  }
  function event(element, attribute, value) {
    return [['padEvent', element.getAttribute(attribute)], ['padValue', element.hasAttribute('pad-value') ? element.getAttribute('pad-value') : value]];
  }
  document.addEventListener('click', function (e) {
    var element = e.target.closest('[pad-click]');
    var region = element && element.closest('[data-pad-live]');
    if (!region) return;
    e.preventDefault();
    send(region, event(element, 'pad-click', ''));
  });
  document.addEventListener('submit', function (e) {
    var form = e.target;
    var region = form.hasAttribute('pad-submit') && form.closest('[data-pad-live]');
    if (!region) return;
    e.preventDefault();
    var fields = [];
    new FormData(form).forEach(function (value, key) { if (typeof value === 'string') fields.push([key, value]); });
    send(region, fields.concat(event(form, 'pad-submit', '')));
  });
  document.addEventListener('change', function (e) {
    var element = e.target.closest('[pad-change]');
    var region = element && element.closest('[data-pad-live]');
    if (!region) return;
    var value = element.type === 'checkbox' ? (element.checked ? element.value : '') : element.value;
    send(region, (element.name ? [[element.name, value]] : []).concat(event(element, 'pad-change', value)));
  });
})();
SCRIPT;

  }

?>

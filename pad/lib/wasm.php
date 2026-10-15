<?php

  // PAD on PHP compiled to WebAssembly - an application packed by wasm/app.mjs (pad pack),
  // running in a browser tab or its service worker. The runtime there (wasm/app/pad-app.js)
  // passes PAD_WASM in $_SERVER, a key no HTTP request can fill: a request header arrives
  // as HTTP_PAD_WASM. A page asks it to leave out what needs a server - a mail server, a
  // remote fetch - and the engine asks it where the browser works differently (padSse: one
  // PHP answers the whole tab, so a stream there is one round).

  function padWasm () {

    return ( $_SERVER ['PAD_WASM'] ?? '' ) !== '';

  }

?>

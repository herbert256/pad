<?php

  // The tags that reach outside the running request: {curl} fetches any url - SELF:// is
  // this host, whatever host and mount prefix that is - {get} fetches a page of this same
  // application, and {ajax} fetches nothing itself but emits the div and XMLHttpRequest
  // for the browser to do it.
  //
  // The ajax markup carries a fresh random id each time, and what {get} answers counts
  // whatever the pages suite currently holds - those two are pinned by shape, not by value.

  return [

    [ 'curl fetches a url, SELF:// meaning this host',
      <<<'PAD'
      {curl 'SELF://regression2/?pairing&padInclude'}
      PAD,
      'Hello:abc' ],

    [ 'get fetches a page of this application, bare and escaped',
      <<<'PAD'
      {get 'pages/index'}
      PAD,
      '/\d+ pages, \d+ tests, \d+ failed/' ],

    [ 'ajax emits the div and the script that fills it',
      <<<'PAD'
      {ajax 'pages/index'}
      PAD,
      '/<div id="[A-Za-z0-9]+">.*XMLHttpRequest/' ],

  ];

?>

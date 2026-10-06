<?php

  // A section whose rendering holds the visitor's CSRF token - a {form}, {csrf}, a live
  // region - or this request's CSP nonce is rendered every time and never kept: the copy
  // went to every later visitor, who got the first one's token - to post with on that
  // visitor's behalf - and a nonce no later Content-Security-Policy header names. The
  // second section of each name finds no stored copy and renders its own content.

  padFragmentForget ( 'fwTokenSection' );
  padFragmentForget ( 'fwNonceSection' );

?>

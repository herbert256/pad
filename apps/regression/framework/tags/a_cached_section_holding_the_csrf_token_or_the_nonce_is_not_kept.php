<?php

  // A section whose rendering holds the visitor's CSRF token - a {form}, {csrf}, a live
  // region - is rendered every time and never kept: the copy went to every later visitor,
  // who got the first one's token to post with on that visitor's behalf. The second section
  // of that name finds no stored copy and renders its own content. One holding the CSP
  // nonce is kept with a marker in its place, which a hit fills with the nonce of the
  // request it answers - the second section of that name is the first one's copy.

  padFragmentForget ( 'fwTokenSection' );
  padFragmentForget ( 'fwNonceSection' );

?>

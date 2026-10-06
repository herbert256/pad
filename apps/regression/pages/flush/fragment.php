<?php

  // A request for one response fragment is answered with that fragment alone, a {flush}
  // above it on the page or in its wrapper notwithstanding: the flush sent the part of the
  // page above it first, and the fragment an HTMX swap or an {ajax fragment=} put in its
  // place came with the page's head in front of it.

  $padFragmentOnly = 'part';

?>

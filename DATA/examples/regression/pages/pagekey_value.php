<?php

  // The query key that names a page carries a value to it: ?pagekey_shown=42 is the page
  // pagekey_shown with $pagekey_shown = 42. Only a bare key - the crawl's own ?pagekey_shown
  // - is the page's name and nothing else; the value was dropped with it.

  $answer = trim ( padCurl ( $padHost . 'regression/pages/?pagekey_shown=42&padInclude' ) ['data'] );

?>

<?php

  // The double @page@ check runs when wrappers compose - a full fetch, which the bare
  // suite fetches never are - so the broken wrapper is fetched here and its answer shown,
  // brace-escaped so the error text renders as the text it is.

  $out = padEscape ( padCurl ( $padHost . 'regression/errors/?syntax/badwrap/index' ) ['data'] );

?>
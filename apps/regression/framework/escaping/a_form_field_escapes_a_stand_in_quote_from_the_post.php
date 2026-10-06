<?php

  // A field refills from the post, which can carry a quote's stand-in (U+E022) in its value;
  // it is escaped as the quote it will become at output, so it cannot break out of the
  // input's value= and add an event handler. The post is set here, so the case is one fetch.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST ['padForm'] = 'c';
  $_POST ['email']   = "hi\u{E022} onfocus=1";

?>

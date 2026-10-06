<?php

  // The attributes of a form are read the way a browser reads them, one after the other:
  // an action written straight after a quote or a slash - method="post"action=... - is the
  // form's action, text inside another attribute's value - title="see action=?here" - is
  // none, and a method that is not exactly post - " post" - is a GET. Each was looked for
  // anywhere in the tag after a space, so the first three were taken for forms posting
  // here and handed the token, and the GET form put it into its URL.

  $padCsrf = TRUE;

?>

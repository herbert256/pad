<?php

  // A relative action is resolved against the page's <base href>: under a base on another
  // site, action="save" posts there, and gets no token; no action, or an empty one, is the
  // page's own address whatever the base. The base was not looked at, and save took the
  // token to elsewhere.example.

  $padCsrf = TRUE;

?>

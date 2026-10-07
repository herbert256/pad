<?php

  // An action written with HTML5's named character references - &Tab; and &sol; - is read
  // the way a browser reads it: /&Tab;/elsewhere.example/ and &sol;&sol;elsewhere.example/
  // post to that site, and were handed the session's token as paths on this one, since only
  // the references HTML 4 knew were decoded. &quest;x is ?x, this site.

  $padCsrf = TRUE;

?>

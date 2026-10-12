<?php

  // A logged-in visitor is logged in below a {flush} too: the session they brought was not
  // opened before the headers went out, and {auth} below it took them for a guest. The page
  // logs in through flush/auth_login, then asks flush/auth_inner with that session.

  $login  = padCurl ( [ 'url' => $padHost . 'regression/pages/?flush/auth_login&padInclude' ] );
  $inner  = padCurl ( [ 'url' => $padHost . 'regression/pages/?flush/auth_inner&padInclude', 'cookies' => $login ['cookies'] ] );
  $answer = trim ( $inner ['data'] );

?>

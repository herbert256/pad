<?php

  // A button with formmethod="get" sends its posting form by GET, the token in the URL -
  // history and Referer - as a GET form would: such a form gets no token, as a GET form
  // gets none. formmethod="post" changes nothing.

  $padCsrf = TRUE;

?>

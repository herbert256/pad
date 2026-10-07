<?php

  // Forms are found the way a browser finds them, tag after tag: what stands in a script, a
  // style, a textarea or a comment is no tag, and neither is the text of an attribute value.
  // A quote left open in '<form title="' inside a script read on through the real form tag
  // after it, which got no token and was answered 403; a </form> in a value or a <form> in
  // a comment ended the form early, and the button posting to elsewhere.example after it was
  // never seen; a form in a comment or a textarea got a token of its own.

  $padCsrf = TRUE;

?>

<?php

  // A button anywhere in the page belongs to the form its form= names, and sends that form
  // where its formaction= says: a form with such a button posting to another site gets no
  // token - a {form} loses the one it wrote - where only the buttons inside the form's own
  // text were looked at, and a click on the button outside handed the token over.

  $padCsrf = TRUE;

?>

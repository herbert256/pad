<?php

  // {csrf}: the hidden form field holding the CSRF token of the visitor's session, for a
  // form to carry back - padCsrfField in lib/csrf.php. {csrf token} is the bare token, for
  // a script that sends it in an X-CSRF-Token header. The session starts here if the
  // visitor has none yet.
  //
  // With $padCsrf on, every POST form of the page gets the field anyway; {csrf} is for a
  // form the page builds some other way, and for an application that checks posts itself
  // with padCsrfValid ().

  if ( padTagParm ( 'token', FALSE ) )
    return padCsrfToken ();

  return padCsrfField ();

?>

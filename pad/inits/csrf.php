<?php

  // With $padCsrf on, a request that changes state - a POST, or a PUT, PATCH or DELETE -
  // must bring back the token of the visitor's session (lib/csrf.php) or it is answered 403
  // before any of the application runs and before a posted value becomes a variable. A
  // request from another site cannot know the token, so it ends here.
  //
  // Off by default, since every POST an application receives - a form, an API call, a
  // script's fetch - has to carry the token once it is on. An application that takes posts
  // from elsewhere on some pages can switch it off for those in its _config/config.php,
  // which runs knowing $padPage.

  if ( $padCsrf and padCsrfUnsafe () and ! padCsrfValid () )
    padRefuse ( 403, 'Forbidden: this form was not sent from this site, or the page it came '
                   . 'from is too old - reload the page and send it again (the CSRF token is '
                   . 'missing or does not match)' );

?>

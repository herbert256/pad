<?php

  // The topic's author as the component shows it - the name and the role. What a provider
  // returns is in the page source for every visitor, so the e-mail address is left out:
  // nothing showed it, and every visitor got it.

  return db ( "RECORD id, username, role, created_at FROM users WHERE id={0}",
            [ $padProviders ['topic'] ['user_id'] ] );

?>

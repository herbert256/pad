<?php

  // A fixture: the login page's work done - the visitor logged in and sent back to the
  // address padAuthRequire kept, else to the default page.

  padLogin ( [ 'id' => 1 ] );

  padRedirectIntended ( 'request/vars' );

?>

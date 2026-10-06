<?php

  // A fixture: {redirect} to an absolute address, the tag run as a template runs it. An
  // action page - the tag ends the request through padRedirect's exit - so the walker
  // leaves it out; request/redirect_absolute fetches it deliberately.

  echo padCode ( "{redirect 'https://example.com/landing', \$from = 'pad'}" );

?>

<?php

  // A fixture: {redirect} to an absolute address with a fragment, the tag run as a template
  // runs it. An action page - the tag ends the request through padRedirect's exit - so the
  // walker leaves it out; request/redirect_fragment fetches it deliberately.

  echo padCode ( "{redirect 'https://example.com/x?a=1#top', \$from = 'pad'}" );

?>

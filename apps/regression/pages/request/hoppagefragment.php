<?php

  // A fixture: {pad:redirect} to a page of this application with a #fragment, the tag run as a
  // template runs it. An action page - the tag ends the request through padRedirect's exit -
  // so the walker leaves it out; request/redirect_page_fragment fetches it deliberately.

  echo padCode ( "{pad:redirect 'request/redirect_fragment#top'/}" );

?>

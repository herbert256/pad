<?php

  // Body of the {redirect} tag: sends a Location header to $padParm and ends the request.
  // Every variable {set} at this level is passed along as a query parameter. padRedirect()
  // does not return, so nothing after this line runs.

  padRedirect ( $padParm, $padSetLvl [$pad] );

?>
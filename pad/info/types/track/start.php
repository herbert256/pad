<?php

  // Starts the 'track' info mode, which files what a request received and returned.
  //
  // Loads the mode's helpers and, when $padInfoTrackFileRequest is set, writes the incoming
  // side - headers, GET, POST, FILES, cookies, the body and $_SERVER, each redacted as an
  // error report keeps it (padInfoTrackStart) - to DATA/track/requests/<log>-entry.json;
  // info/types/track/end.php merges it with the response. $_ENV is left out: the
  // environment is where the server keeps its own secrets.

  global $padInfoTrackFileRequest;

  include_once PAD . 'info/types/track/_lib.php';

  if ( $padInfoTrackFileRequest )
    padInfoTrackStart ();

?>

<?php

  // Starts the 'track' info mode, which files what a request received and returned.
  //
  // Loads the mode's helpers and, when $padInfoTrackFileRequest is set, writes the incoming
  // side - headers, GET, POST, FILES, cookies, raw body, $_SERVER, $_ENV - to
  // DATA/track/requests/<log>-entry.json; info/types/track/end.php merges it with the response.

  global $padInfoTrackFileRequest;

  include_once PAD . 'info/types/track/_lib.php';

  if ( $padInfoTrackFileRequest )
    padInfoTrackStart ();

?>
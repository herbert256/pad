<?php

  // Establishes the three identifiers that tie a request to its history.
  //
  // $padSesID survives across requests (cookie, or padSesID in the URL when cookies are not
  // an option), $padReqID is minted fresh for this request, and $padRefID keeps the previous
  // request's id so the chain of pages can be reconstructed. $padLog defaults to the request
  // id and names the log and dump files written under DATA.
  //
  // Each is only filled in if not already set, so a restart keeps the ids of the run it
  // replaces. inits/cookies.php sends the session and request ids back to the browser.

  $padSesID = $padSesID ?? $_COOKIE ['padSesID'] ?? $_REQUEST ['padSesID'] ?? padRandomString();
  $padRefID = $padRefID ?? $padReqID ?? $_COOKIE ['padReqID'] ?? $_REQUEST ['padReqID'] ?? '';
  $padReqID = $padReqID ?? padRandomString();

  $padLog = $padLog ?? $padReqID;

?>
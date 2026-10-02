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
  //
  // An id that comes from the browser is taken only when it has the shape PAD mints - eight
  // letters and digits (padValidID) - since it goes on into a cookie header, SQL text and
  // file names; anything else is a new session, or no previous request.

  if ( ! isset ( $padSesID ) ) {
    $padSesID = $_COOKIE ['padSesID'] ?? $_REQUEST ['padSesID'] ?? '';
    if ( ! padValidID ( $padSesID ) )
      $padSesID = padRandomString ();
  }

  if ( ! isset ( $padRefID ) ) {
    $padRefID = $padReqID ?? $_COOKIE ['padReqID'] ?? $_REQUEST ['padReqID'] ?? '';
    if ( ! padValidID ( $padRefID ) )
      $padRefID = '';
  }

  $padReqID = $padReqID ?? padRandomString();

  $padLog = $padLog ?? $padReqID;

?>
<?php


  /**
   * Creates a shortened URL with serialized variables.
   *
   * Stores page and variables in database and returns a short
   * URL that can be used to restore the state later.
   *
   * @param string $padPage The page to link to.
   * @param array  $vars    Variables to store with the link.
   *
   * @return string The shortened URL.
   *
   * @global int    $padFastLink Length of random string for link.
   * @global string $padSesID    Current session ID.
   * @global string $padReqID    Current request ID.
   * @global string $padHost     Server host URL.
   */
  function padFastLink ( $padPage, $vars ) {

    global $padFastLink, $padSesID, $padReqID, $padHost, $padScript;

    $vars ['padPage']  = $padPage;
    $vars ['padSesID'] = $padSesID;
    $vars ['padRefID'] = $padReqID;

    $fast = padRandomString ($padFastLink);

    padDb (
      "insert into links values('{0}','{1}')",
      [$fast, serialize($vars)]
    );

    return "$padHost$padScript?$fast";

  }


  /**
   * Redirects browser to a URL and terminates.
   *
   * Appends variables as query string, handles protocol prefixes,
   * adds session/request IDs for same-host URLs, and exits with 302.
   *
   * @param string $go   The URL or path to redirect to.
   * @param array  $vars Optional variables to append as query string.
   *
   * @return void Never returns - calls padExit(302).
   *
   * @global string $padHost  Server host URL.
   * @global string $padGoExt Default URL prefix.
   */
  function padRedirect ( $go, $vars=[] ) {

    global $padHost, $padGoExt;

    foreach ( $vars as $padK => $padV )
      $go .= "&$padK=" . urlencode($padV);

    if ( ! strpos($go, '://') )
      $go = $padGoExt . $go;

    $go = str_replace('SELF://', "$padHost/", $go);

    if  ( str_starts_with ( $go, $padHost ) ) {
      $go = padAddGet ( $go, 'padSesID', $GLOBALS ['padSesID'] );
      $go = padAddGet ( $go, 'padReqID', $GLOBALS ['padReqID'] );
    }

    padHeader ("Location: $go");

    padExit (302);

  }


  /**
   * Schedules an internal page restart.
   *
   * Sets global variables to trigger a restart to a different
   * page after current processing completes.
   *
   * @param string $go   The page to restart to.
   * @param array  $vars Optional variables for the restart.
   *
   * @return null Always returns NULL.
   */
  function padRestart ( $go, $vars=[] ) {

    $GLOBALS ['padRestart']     = $go;
    $GLOBALS ['padRestartVars'] = $vars;

    return NULL;

  }


?>
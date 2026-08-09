<?php

  // One framework case per request: ?framework/run&case=<group>/<case>.
  //
  // The case's .php - when it has one - is included here, so its variables are this page's
  // variables, exactly the pairing every page has; the returned .pad text is prepended to
  // the (absent) template and rendered as the page. The cases live under _suites/, which
  // the underscore keeps away from the router and the crawl - this page is their one door.

  $padRunCase = $_REQUEST ['case'] ?? '';

  if ( ! preg_match ( '#^[a-zA-Z0-9_-]+/[a-zA-Z0-9_-]+$#', $padRunCase ) )
    return 'no such case';

  $padRunBase = APP . 'framework/_suites/' . $padRunCase;

  if ( ! file_exists ( "$padRunBase.pad" ) )
    return 'no such case';

  if ( file_exists ( "$padRunBase.php" ) )
    include "$padRunBase.php";

  return padFileGet ( "$padRunBase.pad" );

?>

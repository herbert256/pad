<?php

  // Resolving a requested page name to a real page in the application, and fetching one
  // page from inside another.
  //
  // padPageCheck  the guarded entry: rejects anything that is not a safe relative page
  //               name (no //, no trailing /, and no /_ so the _xxx directories stay
  //               private) before handing over to padPage - or, for a name that is no
  //               page of its own, to the bracketed routes (padPageRoute, lib/route.php)
  // padPage       walks the name segment by segment from APP down, and returns the page,
  //               or "$page/index" when the name turned out to be a directory, or FALSE
  // padPageVirtual   a page an entry point supplies the template of: a valid name in an
  //                  existing directory, its own files optional
  // padPageExists    does any of the three page files exist for this base - .php, .pad
  //                  or .html, which is a template like .pad in every way
  // padPageTemplate  the template text for a page base: the .pad, or failing that the
  //                  .html - so when both exist the .pad wins
  // padPageAjax   returns a div plus an XMLHttpRequest that loads another PAD page into
  //               it at the client, carrying the session and request ids along
  // padPageGet    fetches another page server side over curl and returns its body
  //
  // padPageCheck and padPage take the application directory as an optional second
  // argument, so {ajax app=} can ask the same question of another application. The router
  // answers with these, and {page}, {get}, {redirect} and {ajax} ask them too: they used
  // padAppPageCheck, the _include/-style search from the page's own directory up, which
  // knew neither .html pages nor directory indexes - {page 'sub'} was refused where ?sub
  // served sub/index - and found a page beside the current one that the nested build then
  // looked for at the root.

  // A name that is no page of its own may still be one through a bracketed name - the
  // clean URL routes of lib/route.php - and then the answer is that page: products/42 is
  // products/[id]. padPageRoute has the bound values too.

  function padPageCheck ( $page, $app=APP ) {

    return padPageRoute ( $page, $app ) ['page'] ?? FALSE;

  }

  // A page whose template an entry point hands over as text ($padPageSource, see
  // inits/page.php): a name padPageCheck would accept the form of, in a directory of the
  // application that exists - the page's own files need not.

  function padPageVirtual ( $page ) {

    if ( ! preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_\/-]*$/D', $page ) ) return FALSE;
    if ( strpos ( $page, '//' ) !== FALSE )                         return FALSE;
    if ( substr ( $page, -1 ) == '/' )                              return FALSE;
    if ( strpos ( $page, '/_' ) !== FALSE )                         return FALSE;

    return is_dir ( dirname ( APP . $page ) );

  }

  // The test pages of an application live in its _tests/ directory, which no URL reaches.
  // pad test renders them on the command line, and says so with $padTestRun before the
  // engine starts (apps/cli/_commands/render.php); nothing a request sends can set it.

  function padTestPageCheck ( $page ) {

    if ( PHP_SAPI !== 'cli' or ! ( $GLOBALS ['padTestRun'] ?? FALSE ) )
      return FALSE;

    if ( ! preg_match ( '#^_tests/[a-zA-Z0-9][a-zA-Z0-9_/-]*$#D', $page ) or str_contains ( $page, '/_' ) )
      return FALSE;

    return padPageExists ( APP . $page );

  }

  function padPageExists ( $base ) {

    return (    file_exists ( "$base.php"  )
             or file_exists ( "$base.pad"  )
             or file_exists ( "$base.html" ) );

  }

  function padPageTemplate ( $base ) {

    if ( file_exists ( "$base.pad" ) )
      return padFileGet ( "$base.pad" );

    return padFileGet ( "$base.html" );

  }

  function padPage ( $page, $app=APP ) {

    $location = $app;
    $part     = padExplode ( $page, '/' );

    foreach ($part as $key => $value)
      if ( $key == array_key_last($part) and padPageExists ( "$location$value" ) )
        return $page;
      else
        if ( is_dir ( "$location$value" ) )
          $location .= "$value/";
        else
          return FALSE;

    if ( padPageExists ( $location . 'index' ) )
      return "$page/index";
    else
      return FALSE;

  }

  function padPageAjax ( $page, $qry, $app='' ) {

    global $padGoExt, $padHost;

    $ajax = 'padAjax' . md5 ( $page . $qry . $app ) ;
    //$ajax = 'padAjax' . padRandomString(8);

    if ( $app )
      $url = "$padHost$app/?$page$qry";
    else
      $url = "$padGoExt$page$qry";

    $url = padAddIds ( $url );

    // The address goes into the script as one JavaScript string. A page name is no plain
    // word when a route binds it - products/[id] takes any segment - and written as it was,
    // x");alert(1);(" closed the string and ran as the page's own script.

    $url = json_encode ( $url, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE );

    return <<< END
<div id="{$ajax}"></div>

<script>
  {$ajax} = new XMLHttpRequest();
  {$ajax}.onreadystatechange=function() {
    if ({$ajax}.readyState === 4) {
      if ({$ajax}.status === 200) {
        document.getElementById("{$ajax}").innerHTML={$ajax}.responseText;
      } else {
        document.getElementById("{$ajax}").innerHTML={$ajax}.statusText;
      }
    }
  }
  {$ajax}.open("GET",{$url},true);
  {$ajax}.send();
</script>
END;

  }

  function padPageGet ( $page, $qry='' ) {

    global $padGoExt;

    $curl = padCurl ( $padGoExt . $page . $qry );

    if ( ! str_starts_with( $curl ['result'], '2') )
      return padError ("Curl failed: " . $curl['url'] );

    return $curl ['data'];

  }

?>

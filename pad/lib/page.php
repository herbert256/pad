<?php


  /**
   * Validates and resolves a page name.
   *
   * Checks for valid characters, no double slashes, no trailing
   * slash, no underscore directories, then resolves the page.
   *
   * @param string $page The page name to check.
   *
   * @return string|false Resolved page name or FALSE if invalid.
   */
  function padPageCheck ( $page ) {

    if ( ! preg_match ( '/^[a-zA-Z][a-zA-Z0-9_\/-]*$/', $page ) ) return FALSE;
    if ( trim($page) == '' )                                      return FALSE;
    if ( strpos($page, '//') !== FALSE)                           return FALSE;
    if ( substr($page, -1) == '/')                                return FALSE;
    if ( strpos($page, '/_') !== FALSE)                           return FALSE;

    return padPage ( $page );

  }


  /**
   * Resolves a page path to actual file location.
   *
   * Walks directory structure checking for .php/.pad files,
   * returns index if directory has index file.
   *
   * @param string $page The page path to resolve.
   *
   * @return string|false Resolved page path or FALSE if not found.
   */
  function padPage ( $page ) {

    $location = APP;
    $part     = padExplode ( $page, '/' );

    foreach ($part as $key => $value)
      if ( $key == array_key_last($part) and ( file_exists ( "$location$value.php" ) or file_exists ( "$location$value.pad" ) ) )
        return $page;
      else
        if ( is_dir ( "$location$value" ) )
          $location .= "$value/";
        else
          return FALSE;

    if ( file_exists ( $location . 'index.php' ) or file_exists ( $location . 'index.pad' ) )
      return "$page/index";
    else
      return FALSE;

  }



  /**
   * Generates AJAX loading HTML/JS for a page.
   *
   * Returns div and script that asynchronously loads page content
   * via XMLHttpRequest.
   *
   * @param string $page The page to load.
   * @param string $qry  Query string to append.
   *
   * @return string HTML with div and script for async loading.
   */
  function padPageAjax ( $page, $qry ) {

    $ajax = 'padAjax' . padRandomString(8);

    $url = $GLOBALS ['padHost'] . $GLOBALS ['padGo'] . $page . $qry;
    $url = padAddIds ( $url );

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
  {$ajax}.open("GET","{$url}",true);
  {$ajax}.send();
</script>
END;

  }


  /**
   * Fetches page content via HTTP request.
   *
   * Uses curl to request the page and returns the response body.
   *
   * @param string $page The page to fetch.
   * @param string $qry  Optional query string.
   *
   * @return string|false Page content or FALSE on error.
   */
  function padPageGet ( $page, $qry='' ) {

    $curl = padCurl ( $GLOBALS['padGoExt'] . $page . $qry );

    if ( ! str_starts_with( $curl ['result'], '2') )
      return padError ("Curl failed: " . $curl['url'] );

    return $curl ['data'];

  }


?>
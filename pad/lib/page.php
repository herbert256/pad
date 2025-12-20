<?php

  function padPageCheck ( $page ) {

    if ( ! preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_\/-]*$/', $page ) ) return FALSE;
    if ( trim($page) == '' )                                      return FALSE;
    if ( strpos($page, '//') !== FALSE)                           return FALSE;
    if ( substr($page, -1) == '/')                                return FALSE;
    if ( strpos($page, '/_') !== FALSE)                           return FALSE;

    return padPage ( $page );

  }

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

  function padPageAjax ( $page, $qry ) {

    global $padGo, $padHost;

    $ajax = 'padAjax' . padRandomString(8);

    $url = $padHost . $padGo . $page . $qry;
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

  function padPageGet ( $page, $qry='' ) {

    global $padGoExt;

    $curl = padCurl ( $padGoExt . $page . $qry );

    if ( ! str_starts_with( $curl ['result'], '2') )
      return padError ("Curl failed: " . $curl['url'] );

    return $curl ['data'];

  }

?>
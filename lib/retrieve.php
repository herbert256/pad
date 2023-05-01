<?php


  function padPageInclude ( $page, $include=1 ) {

    if ( $include )
      return padGetHtml ( padApp . "pages/$page.html" , TRUE );

    global $pad, $padBase, $padBuildMerge;

    $padBuildModeSave = $GLOBALS['padBuildMode'];
    $GLOBALS['padBuildMode'] = 'demand';

    $padBuildMrg = padExplode ( "pages/$page", '/' );

    include pad . 'build/lib.php';
    include pad . 'build/html.php';

    $GLOBALS['padBuildMode'] = $padBuildModeSave;

    return $padBase [$pad];

  }


  function padPageFunction ( $page, $parms=[], $include=1 ) {

    include pad . 'retrieve/inits.php'; 

    foreach ( $parms as $padK => $padV )
      $$padK = $padV;

    $padPage       = $page;
    $padBuildMode  = ($include) ? 'include' : 'before';
    $padBuildMerge = 'content';

    $GLOBALS['padIgnoreInclude'] = TRUE;
    include pad . 'build/build.php'; 
    unset ( $GLOBALS['padIgnoreInclude'] );

    $padData [$pad]     = [];
    $padData [$pad] [1] = [];

    foreach ( get_defined_vars() as $padK => $padV )
      if ( padValidStore($padK) )
        $padData [$pad] [1] [$padK] = $padV;

    if ( count ( $padData [$pad] [1] ) ) {
      $padKey     [$pad] = 1;
      $padCurrent [$pad] = $padData [$pad] [1];
    } else {
      $padData    [$pad] = padDefaultData ();
      $padKey     [$pad] = 999;
      $padCurrent [$pad] = $padData [$pad] [99];
    }

    $padHtml [$pad] = $padBase [$pad];    

    return include pad . 'retrieve/exits.php'; 
 
  }


  function padTagAsFunction ($tag, $parms) {

    include pad . 'retrieve/inits.php'; 

    $padHtml [$pad] = '{' . $tag . ' ' . $parms . '}';    

    return include pad . 'retrieve/exits.php'; 

  }


  function padPageGet ( $page, $parms=[], $include=1) {

    $query = '';
    foreach ( $parms as $key => $val )
      $query .= "&$key=" . urlencode($val);

    return pad ( $page, $query, $include );

  }


  function padPageAjax ( $page, $parms=[], $include=1) {

    $ajax = 'padAjax' . padRandomString(8);
    $url  = $GLOBALS ['padScript'] . '?padPage=' . $page; 

    foreach ( $parms as $padK => $padV )
      $url .= "&$padK=" . urlencode($padV);

    if ( $include )
      $url .= '&padInclude=1';

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


?>
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


  function padPageGet ( $page, $parms=[], $include=1) {

    $query = '';
    foreach ( $parms as $padK => $padV )
      $query .= "&$padK=" . urlencode($padV);

    return pad ( $page, $query, $include );

  }


  function padPageAjax ( $page, $parms=[], $include=1) {

    if ( ! isset($GLOBALS['padAjax']) )
      $GLOBALS['padAjax'] = 0;

    global $padAjax; 
      
    $padAjax++;

    $ajax = str_replace('/', '', "$page$padAjax".$GLOBALS['padPage']);

    $url = '?padPage=' . $page; 

    foreach ( $parms as $padK => $padV )
      $url .= "&$padK=" . urlencode($padV);

    if ( $include )
      $url .= '&padInclude=1';

    return <<< END
<div id="padAjax{$ajax}"></div>

<script>
  padAjax{$ajax} = new XMLHttpRequest();
  padAjax{$ajax}.onreadystatechange=function() {
    if (padAjax{$ajax}.readyState === 4) {
      if (padAjax{$ajax}.status === 200) {
        document.getElementById("padAjax{$ajax}").innerHTML=padAjax{$ajax}.responseText;
      } else {
        document.getElementById("padAjax{$ajax}").innerHTML=padAjax{$ajax}.statusText;
      }
    }
  }
  padAjax{$ajax}.open("GET","{$url}",true);
  padAjax{$ajax}.send();
</script>
END;

  }


  function padPageFunction ( $padRetrievePage, $padRetrieveParms=[], $include=1 ) {

    include pad . 'retrieve/inits.php'; 

    foreach ( $padRetrieveParms as $padK => $padV )
      $$padK = $padV;

    $padPage          = $padRetrievePage;
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
      $padData [$pad] = padDefaultData ();
      $padKey     [$pad] = 999;
      $padCurrent [$pad] = $padData [$pad] [99];
    }

    $padHtml [$pad] = $padBase [$pad];    

    return include pad . 'retrieve/exits.php'; 
 
  }


  function padRetrieveContent ( $padRetrieveContent ) {

    include pad . 'retrieve/inits.php'; 

    $padHtml [$pad] = $padRetrieveContent;    

    return include pad . 'retrieve/exits.php'; 

  }


  function padTagAsFunction ($tag, $parms) {

    include pad . 'retrieve/inits.php'; 

    $padHtml [$pad] = '{' . $tag . ' ' . $parms . '}';    

    return include pad . 'retrieve/exits.php'; 

  }


?>
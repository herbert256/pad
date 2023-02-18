<?php


  function padPageInclude ( $page, $include=1 ) {

    if ( $include )
      return padGetHtml ( APP . "pages/$page.html" , TRUE );

    global $pad, $padBase, $padBuildMerge;

    $padBuildModeSave = $GLOBALS['padBuildMode'];
    $GLOBALS['padBuildMode'] = 'demand';

    $padBuildMrg = padExplode ( "pages/$page", '/' );

    include PAD . 'build/lib.php';
    include PAD . 'build/html.php';

    $GLOBALS['padBuildMode'] = $padBuildModeSave;

    return $padBase [$pad];

  }


  function padPageGet ( $page, $parms=[], $include=1, $app=[] ) {

    if ( ! $app )
      $app = GLOBALS['app'];

    $query = '';
    foreach ( $parms as $padK => $padV )
      $query .= "&$padK=" . urlencode($padV);

    return pad ( $app, $page, $query, $include );

  }


  function padPageAjax ( $page, $parms=[], $include=1, $app='' ) {

    if ( ! $app )
      $app = GLOBALS['app'];

    if ( ! isset($GLOBALS['padAjax']) )
      $GLOBALS['padAjax'] = 0;

    global $padAjax, $padApp; 
      
    $padAjax++;

    $ajax = str_replace('/', '', "$app$page$padAjax".$GLOBALS['page']);

    $url = $padApp . $app . '&page=' . $page; 

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

    include PAD . 'retrieve/inits.php'; 

    foreach ( $padRetrieveParms as $padK => $padV )
      $$padK = $padV;

    $page          = $padRetrievePage;
    $padBuildMode  = ($include) ? 'include' : 'before';
    $padBuildMerge = 'content';

    $GLOBALS['padIgnoreInclude'] = TRUE;
    include PAD . 'build/build.php'; 
    unset ( $GLOBALS['padIgnoreInclude'] );

    $padData [$pad]     = [];
    $padData [$pad] [1] = [];

    foreach ( get_defined_vars() as $padK => $padV )
      if ( padValidStore($padK) )
        $padData [$pad] [1] [$padK] = $padV;

    foreach ( $padRetrieveParms as $padK => $padV )
      $padData [$pad] [1] [$padK] = $padV;

    if ( count ( $padData [$pad] [1] ) ) {
      $padKey     [$pad] = 1;
      $padCurrent [$pad] = $padData [$pad] [1];
    } else
      $padData [$pad] = padDefaultData ();

    $padHtml [$pad] = $padBase [$pad];    

    return include PAD . 'retrieve/exits.php'; 
 
  }


  function padRetrieveContent ( $padRetrieveContent ) {

    include PAD . 'retrieve/inits.php'; 

    $padHtml [$pad] = $padRetrieveContent;    

    return include PAD . 'retrieve/exits.php'; 

  }


  function padTagAsFunction ($tag, $parms) {

    return padRetrieveContent ( '{' . $tag . ' ' . $parms . '}' );

  }


?>
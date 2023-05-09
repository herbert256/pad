<?php


  function padPageCheck ( $page ) {

    if ( ! preg_match ( '/^[a-zA-Z][a-zA-Z0-9_\/]*$/', $page ) )  return FALSE;
    if ( trim($page) == '' )                                      return FALSE;
    if ( strpos($page, '//') !== FALSE)                           return FALSE;
    if ( substr($page, -1) == '/')                                return FALSE;

    $location = padApp . "pages";
    $part     = padExplode ($page, '/');
    
    foreach ($part as $key => $value) {
      
      if ($value == '_lib')   return FALSE;
      if ($value == '_inits') return FALSE;
      if ($value == '_exits') return FALSE;

      if ( $key == array_key_last($part)
            and (padExists("$location/$value.php") or padExists("$location/$value.html") ) )
        return TRUE; 

      if ( is_dir ("$location/$value") )
        $location.= "/$value";
      else
        return FALSE;
      
    }
    
    return ( padExists("$location/index.php") or padExists("$location/index.html") );
    
  }


  function padPageSet ( $page ) {

    $location = padApp . "pages";
    $part     = padExplode ($page, '/');
    
    foreach ($part as $key => $value)
      if ( $key == array_key_last($part)
            and (padExists("$location/$value.php") or padExists("$location/$value.html") ) )
        return $page; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
   
    return "$page/index";

  }


  function padPageAjax ( $page, $qry ) {

    $ajax = 'padAjax' . padRandomString(8);
 
    $url = $GLOBALS ['padGo'] . $page . $qry;

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

  function padPageGet ( $page, $qry ) {

    $url = $GLOBALS['padGoExt'] . $page . $qry;
    
    $return = padCurlData ($url);

    $return = str_replace('}', '&close;', $return);

    return $return;

  }
  

  function padTagAsFunction ( $tag, $parms ) {

    include pad . 'page/inits.php'; 

    $padHtml [$pad] = '{' . $tag . ' ' . $parms . '}';    

    return include pad . 'page/exits.php'; 

  }


?>
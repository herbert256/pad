<?php


  function padPageGetName () {

    global $pad, $padPage, $padOpt;

    $now = $padPage;
    $new = padTagParm ( 'page', $padOpt [$pad] [1] ); 

    if ( ! $now ) return 'examples/dummy';
    if ( ! $new ) return 'examples/dummy';

    if ( str_starts_with ( $new, '/') ) {
      $chk = substr($new, 1);    
      if ( padPageCheck ( $chk ) )
        return padPageSet ( $chk );
    }

    if ( str_starts_with ( $new, './') ) {
      $chk = substr($now, 0, strrpos($now, '/')) . '/' . substr ( $new, 2);
      if ( padPageCheck ( $chk ) )
        return padPageSet ( $chk );
    }

    if ( strrpos($now, '/') !== FALSE ) {
      $chk = substr($now, 0, strrpos($now, '/')) . '/' . $new;
      if ( padPageCheck ( $chk ) )
         return padPageSet ( $chk );
     }
     
    if ( strrpos($now, '/') === FALSE ) {
      $chk = $new;
      if ( padPageCheck ( $chk ) )
         return padPageSet ( $chk ); 
    }

    $chk = $new;
    if ( padPageCheck ( $chk ) )
      return padPageSet ( $chk ); 

    return 'examples/dummy';
  
  }


  function padPageCheck ( $page ) {

    if ( ! padValidPage ($page) )
      return FALSE;

    $location = padApp . "pages";
    $part     = padExplode ($page, '/');
    
    foreach ($part as $key => $value) {
      
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
  

?>
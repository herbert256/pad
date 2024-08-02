<?php


  function padPageCheck ( $page, $check=1 ) {

    if ( $check and ! padValidPage ($page) )
      return FALSE;

    $location = padApp;
    $part     = padExplode ($page, '/');

    foreach ($part as $key => $value) {
      
      if ( $key == array_key_last($part)
            and (file_exists("$location$value.php") or file_exists("$location$value.pad") ) )
        return TRUE; 

      if ( is_dir ("$location$value") )
        $location .= "$value/";
      else
        return FALSE;
      
    }
    
    return ( file_exists("$location"."index.php") or file_exists("$location"."index.pad") );
    
  }


  function padPageSet ( $page ) {

    $location = padApp;
    $part     = padExplode ($page, '/');
    
    foreach ($part as $key => $value)
      if ( $key == array_key_last($part)
            and (file_exists("$location$value.php") or file_exists("$location$value.pad") ) )
        return $page; 
      elseif ( is_dir ("$location$value") )
        $location.= "$value/";
   
    return "$page/index";

  }


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


  function padPageGet ( $page, $qry ) {

    $curl = padCurl ( $GLOBALS['padGoExt'] . $page . $qry );

    if ( ! str_starts_with( $curl ['result'], '2') )
      return padError ("Curl failed: " . $curl['url'] );
 
    return $curl ['data'];

  }
  

?>
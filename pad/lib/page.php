<?php

  
  function padPageFunction ( ) {

    foreach ($GLOBALS as $padK => $padV )
      if ( substr($padK, 0, 3) == 'pad' )
        global $$padK;
      
    include pad . 'pad/start.php';
    include pad . 'pad/setup.php';
    include pad . 'build/build.php';   
    include pad . 'pad/level.php'; 
    include pad . 'pad/end.php';

    return $padPad [$pad+1];

  }



  function padPageGetName ( $page = '' ) {

    global $pad, $padPage, $padOpt;

    $now = $padPage;

    if ( $page )
      $new = $page;
    else
      $new = padTagParm ( 'page', $padOpt [$pad] [1] ); 

    if ( ! $new ) 
      return '';

    if ( str_starts_with ( $new, '/') ) {
      $chk = substr($new, 1);    
      if ( padPageCheck ( $chk, 0 ) )
        return padPageSet ( $chk );
    }

    if ( str_starts_with ( $new, './') ) {
      $chk = substr($now, 0, strrpos($now, '/')) . '/' . substr ( $new, 2);
      if ( padPageCheck ( $chk, 0 ) )
        return padPageSet ( $chk );
    }

    if ( strrpos($now, '/') !== FALSE ) {
      $chk = substr($now, 0, strrpos($now, '/')) . '/' . $new;
      if ( padPageCheck ( $chk, 0 ) )
         return padPageSet ( $chk );
     }
     
    if ( strrpos($now, '/') === FALSE ) {
      $chk = $new;
      if ( padPageCheck ( $chk, 0 ) )
         return padPageSet ( $chk ); 
    }

    $chk = $new;
    if ( padPageCheck ( $chk, 0 ) )
      return padPageSet ( $chk ); 

    return '';
  
  }


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
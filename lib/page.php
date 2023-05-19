<?php

  function padPageAddSet ( $set, $content ) {

    if ( count ( $set ) ) {
    
      $open = '{pad ';
      $close = '{/pad}';

      foreach ( $set as $key => $val )
        if ( $open == '{pad ' )
          $open .= " \$$key='$val'";
        else
          $open  = ",\$$key='$val'";

      $open .= '}';

    } else 

      $open = $close = '';

    return "$open$content$close";

  }



  function padPageInsideFunction ( $padFunctionParms ) {

    include pad . 'page/push.php';

    foreach ( $GLOBALS as $key => $val ) 
      if ( substr($key, 0, 6) == 'padSeq' ) 
        unset ( $GLOBALS [$key] );
 
    foreach ($GLOBALS as $key => $val )
      if ( substr($key, 0, 3) == 'pad' )
        global $$key;

    unset($key);
    unset($val);

    include pad . 'page/reset.php';
    include pad . 'page/start.php';

    if ( count ($padFunctionParms) ) {
      $padData    [$pad]     = [];
      $padData    [$pad] [1] = $padFunctionParms;
      $padDefault [$pad]     = FALSE;
    }

    include pad . 'build/build.php';   

    foreach ( get_defined_vars () as $padK => $padV )
      if ( substr($padK, 0, 3) <> 'pad' and ! isset ( $padCurrent [$pad] [$padK] ) )
        $padCurrent [$pad] [$padK] = $padV;

    include pad . 'page/level.php'; 
    include pad . 'page/end.php';
    include pad . 'page/pop.php';

    return $padHtml [$pad+1];
  
  }
 

  function padPageGetName ( $page = '' ) {

    global $pad, $padPage, $padOpt;

    $now = $padPage;

    if ( $page )
      $new = $page;
    else
      $new = padTagParm ( 'page', $padOpt [$pad] [1] ); 

    if ( ! $new ) 
      return 'examples/dummy';

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

    return 'examples/dummy';
  
  }


  function padPageCheck ( $page, $check=1 ) {

    if ( $check and ! padValidPage ($page) )
      return FALSE;

    $location = padApp;
    $part     = padExplode ($page, '/');

    foreach ($part as $key => $value) {
      
      if ( $key == array_key_last($part)
            and (padExists("$location$value.php") or padExists("$location$value.html") ) )
        return TRUE; 

      if ( is_dir ("$location$value") )
        $location .= "$value/";
      else
        return FALSE;
      
    }
    
    return ( padExists("$location"."index.php") or padExists("$location"."index.html") );
    
  }


  function padPageSet ( $page ) {

    $location = padApp;
    $part     = padExplode ($page, '/');
    
    foreach ($part as $key => $value)
      if ( $key == array_key_last($part)
            and (padExists("$location$value.php") or padExists("$location$value.html") ) )
        return $page; 
      elseif ( is_dir ("$location$value") )
        $location.= "$value/";
   
    return "$page/index";

  }


  function padPageAjax ( $page, $qry ) {

    $ajax = 'padAjax' . padRandomString(8);
 
    $url = $GLOBALS ['padGo'] . $page . $qry;
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
      return padError ("Curl failed: $url");
 
    return $curl ['data'];

  }
  

?>
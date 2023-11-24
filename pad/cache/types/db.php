<?php


  function padCacheInit ($url, $etag) {
    
    global $padCacheDbConnect, $padCacheDbHost, $padCacheDbUser, $padCacheDbPassword, $padCacheDbDatabase;
    
    $padCacheDbConnect = padDbConnect ( $padCacheDbHost, $padCacheDbUser, $padCacheDbPassword, $padCacheDbDatabase );

  }
    

  function padCacheDb ( $sql, $vars=[] ) {
    
    global $padCacheDbConnect;

    return padDbPart2 ( $padCacheDbConnect, $sql, $vars, 'cache' );
    
  }


  function padCacheEtag ($etag) {

    return padCacheDb ( "field age from etag where etag='{0}'", [$etag] );

  }

  
  function padCacheUrl ($url) {

    return padCacheDb ( "record age, etag from url where url='{0}'", [$url] );

  }


  function padCacheGet ($etag) {

    return padCacheDb ( "field data from data where etag='{0}'", [$etag] );

  }


  function padCacheStore ($url, $etag, $data) {

    padCacheDb ( "replace etag values ('{0}', {1})", [$etag,$_SERVER['REQUEST_TIME']] );

    if ( ! $GLOBALS ['padCacheServerNoData'] ) {
      padCacheDb ( "replace url  values ('{0}', {1}, '{2}')", [$url,$_SERVER['REQUEST_TIME'],$etag] );
      padCacheDb ( "replace data values ('{0}', '{1}'     )", [$etag,$data] );
    }

  }

  
  function padCacheUpdate ($url, $etag) {

    padCacheDb ( "update etag set age={0} where etag='{1}'", [$_SERVER['REQUEST_TIME'],$etag] );

    if ( ! $GLOBALS ['padCacheServerNoData'] )
      padCacheDb ( "update url set age={0} where url='{1}'", [$_SERVER['REQUEST_TIME'],$url] );

  }


  function padCacheDelete ($url, $etag) {

    padCacheDb ( "delete from etag where etag='{0}'", [$etag] );
 
    if ( ! $GLOBALS ['padCacheServerNoData'] )
      padCacheDb ( "delete from data where etag='{0}'", [$etag] );

  }


?>
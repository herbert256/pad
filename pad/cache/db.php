<?php


  function pCache_init ($url, $etag) {
    
    global $pCache_db_connect, $pCache_db_host, $pCache_db_user, $pCache_db_password, $pCache_db_database;
    
    $pCache_db_connect = pDb_connect ( $pCache_db_host, $pCache_db_user, $pCache_db_password, $pCache_db_database );

  }
    

  function pCache_db ( $sql, $vars=[] ) {
    
    global $pCache_db_connect;

    return pDb_part2 ( $pCache_db_connect, $sql, $vars, 'cache' );
    
  }


  function pCache_etag ($etag) {

    return pCache_db ( "field age from etag where etag='{0}'", [$etag] );

  }

  
  function pCache_url ($url) {

    return pCache_db ( "record age, etag from url where url='{0}'", [$url] );

  }


  function pCache_get ($etag) {

    return pCache_db ( "field data from data where etag='{0}'", [$etag] );

  }


  function pCache_store ($url, $etag, $data) {

    pCache_db ( "replace etag values ('{0}', {1})", [$etag,$_SERVER['REQUEST_TIME']] );

    if ( ! $GLOBALS['pCache_server_no_data'] ) {
      pCache_db ( "replace url  values ('{0}', {1}, '{2}')", [$url,$_SERVER['REQUEST_TIME'],$etag] );
      pCache_db ( "replace data values ('{0}', '{1}'     )", [$etag,$data] );
    }

  }

  
  function pCache_update ($url, $etag) {

    pCache_db ( "update etag set age={0} where etag='{1}'", [$_SERVER['REQUEST_TIME'],$etag] );

    if ( ! $GLOBALS['pCache_server_no_data'] )
      pCache_db ( "update url set age={0} where url='{1}'", [$_SERVER['REQUEST_TIME'],$url] );

  }


  function pCache_delete ($url, $etag) {

    pCache_db ( "delete from etag where etag='{0}'", [$etag] );
 
    if ( ! $GLOBALS['pCache_server_no_data'] )
      pCache_db ( "delete from data where etag='{0}'", [$etag] );

  }


?>
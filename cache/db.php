<?php


  function pad_cache_init ($url, $etag) {
    
    global $pad_cache_db_connect, $pad_cache_db_host, $pad_cache_db_user, $pad_cache_db_password, $pad_cache_db_database;
    
    $pad_cache_db_connect = pad_db_connect ( $pad_cache_db_host, $pad_cache_db_user, $pad_cache_db_password, $pad_cache_db_database );

  }
    

  function pad_cache_db ( $sql, $vars=[] ) {
    
    global $pad_cache_db_connect;

    return pad_db_part2 ( $pad_cache_db_connect, $sql, $vars, 'cache' );
    
  }


  function pad_cache_etag ($etag) {

    return pad_cache_db ( "field age from etag where etag='{0}'", [$etag] );

  }

  
  function pad_cache_url ($url) {

    return pad_cache_db ( "record age, etag from url where url='{0}'", [$url] );

  }


  function pad_cache_get ($etag) {

    return pad_cache_db ( "field data from data where etag='{0}'", [$etag] );

  }


  function pad_cache_store ($url, $etag, $data) {

    pad_cache_db ( "replace etag values ('{0}', {1})", [$etag,$_SERVER['REQUEST_TIME']] );

    if ( ! $GLOBALS['pad_cache_server_no_data'] ) {
      pad_cache_db ( "replace url  values ('{0}', {1}, '{2}')", [$url,$_SERVER['REQUEST_TIME'],$etag] );
      pad_cache_db ( "replace data values ('{0}', '{1}'     )", [$etag,$data] );
    }

  }

  
  function pad_cache_update ($url, $etag) {

    pad_cache_db ( "update etag set age={0} where etag='{1}'", [$_SERVER['REQUEST_TIME'],$etag] );

    if ( ! $GLOBALS['pad_cache_server_no_data'] )
      pad_cache_db ( "update url set age={0} where url='{1}'", [$_SERVER['REQUEST_TIME'],$url] );

  }


  function pad_cache_delete ($url, $etag) {

    pad_cache_db ( "delete from etag where etag='{0}'", [$etag] );
 
    if ( ! $GLOBALS['pad_cache_server_no_data'] )
      pad_cache_db ( "delete from data where etag='{0}'", [$etag] );

  }


?>
<?php

  function pCache_init ($url, $etag) {
    $GLOBALS['pCache_mem'] = new Memcached();
    $GLOBALS['pCache_mem']->addServer($GLOBALS['pCache_memory_host'], $GLOBALS['pCache_memory_port']);
  }

  function pCache_etag ($get) {
    return $GLOBALS['pCache_mem']->get($get);
  }

  function pCache_url ($url) {
    return $GLOBALS['pCache_mem']->get($url);
  }

  function pCache_get ($etag) {
    return $GLOBALS['pCache_mem']->get("x$etag");
  }


  function pCache_store ($url, $etag, $data) {

    $GLOBALS['pCache_mem']->set($etag, $_SERVER['REQUEST_TIME'], $GLOBALS['pCache_server_age']);

    if ( ! $GLOBALS['pCache_server_no_data'] ) {
      $GLOBALS['pCache_mem']->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $GLOBALS['pCache_server_age']);
      $GLOBALS['pCache_mem']->set("x$etag", $data,                          $GLOBALS['pCache_server_age']+10);
     }
    
  }

  
  function pCache_update ($url, $etag) {

    $GLOBALS['pCache_mem']->set($etag, $_SERVER['REQUEST_TIME'], $GLOBALS['pCache_server_age']);

    if ( ! $GLOBALS['pCache_server_no_data'] ) {
      $GLOBALS['pCache_mem']->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $GLOBALS['pCache_server_age']);
      $GLOBALS['pCache_mem']->touch("x$etag", $GLOBALS['pCache_server_age']+10);
    }

  }


  function pCache_delete ($url, $etag) {

    $GLOBALS['pCache_mem']->delete($etag);
 
    if ( ! $GLOBALS['pCache_server_no_data'] )
      $GLOBALS['pCache_mem']->delete("x$etag");

  }


?>
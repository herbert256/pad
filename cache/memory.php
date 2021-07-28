<?php

  function pad_cache_init_memory ($url, $etag) {
    $GLOBALS['pad_cache_mem'] = new Memcached();
    $GLOBALS['pad_cache_mem']->addServer($GLOBALS['pad_cache_memory_host'], $GLOBALS['pad_cache_memory_port']);
  }

  function pad_cache_etag_memory ($get) {
    return $GLOBALS['pad_cache_mem']->get($get);
  }

  function pad_cache_url_memory ($url) {
    return $GLOBALS['pad_cache_mem']->get($url);
  }

  function pad_cache_get_memory ($etag) {
    return $GLOBALS['pad_cache_mem']->get("x$etag");
  }


  function pad_cache_store_memory ($url, $etag, $data) {

    $GLOBALS['pad_cache_mem']->set($etag, $_SERVER['REQUEST_TIME'], $GLOBALS['pad_cache_server_age']);

    if ( ! $GLOBALS['pad_cache_server_no_data'] ) {
      $GLOBALS['pad_cache_mem']->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $GLOBALS['pad_cache_server_age']);
      $GLOBALS['pad_cache_mem']->set("x$etag", $data,                          $GLOBALS['pad_cache_server_age']+10);
     }
    
  }

  
  function pad_cache_update_memory ($url, $etag) {

    $GLOBALS['pad_cache_mem']->set($etag, $_SERVER['REQUEST_TIME'], $GLOBALS['pad_cache_server_age']);

    if ( ! $GLOBALS['pad_cache_server_no_data'] ) {
      $GLOBALS['pad_cache_mem']->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $GLOBALS['pad_cache_server_age']);
      $GLOBALS['pad_cache_mem']->touch("x$etag", $GLOBALS['pad_cache_server_age']+10);
    }

  }


  function pad_cache_delete_memory ($url, $etag) {

    $GLOBALS['pad_cache_mem']->delete($etag);
 
    if ( ! $GLOBALS['pad_cache_server_no_data'] )
        $GLOBALS['pad_cache_mem']->delete("x$etag");

  }


?>
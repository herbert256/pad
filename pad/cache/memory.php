<?php

  function padCacheInit ($url, $etag) {
    $GLOBALS ['padCache_mem'] = new Memcached();
    $GLOBALS ['padCache_mem']->addServer($GLOBALS ['padCache_memory_host'], $GLOBALS ['padCache_memory_port']);
  }

  function padCacheEtag ($get) {
    return $GLOBALS ['padCache_mem']->get($get);
  }

  function padCacheUrl ($url) {
    return $GLOBALS ['padCache_mem']->get($url);
  }

  function padCacheGet ($etag) {
    return $GLOBALS ['padCache_mem']->get("x$etag");
  }


  function padCacheStore ($url, $etag, $data) {

    $GLOBALS ['padCache_mem']->set($etag, $_SERVER['REQUEST_TIME'], $GLOBALS ['padCache_server_age']);

    if ( ! $GLOBALS ['padCache_server_no_data'] ) {
      $GLOBALS ['padCache_mem']->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $GLOBALS ['padCache_server_age']);
      $GLOBALS ['padCache_mem']->set("x$etag", $data,                          $GLOBALS ['padCache_server_age']+10);
     }
    
  }

  
  function padCacheUpdate ($url, $etag) {

    $GLOBALS ['padCache_mem']->set($etag, $_SERVER['REQUEST_TIME'], $GLOBALS ['padCache_server_age']);

    if ( ! $GLOBALS ['padCache_server_no_data'] ) {
      $GLOBALS ['padCache_mem']->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $GLOBALS ['padCache_server_age']);
      $GLOBALS ['padCache_mem']->touch("x$etag", $GLOBALS ['padCache_server_age']+10);
    }

  }


  function padCacheDelete ($url, $etag) {

    $GLOBALS ['padCache_mem']->delete($etag);
 
    if ( ! $GLOBALS ['padCache_server_no_data'] )
      $GLOBALS ['padCache_mem']->delete("x$etag");

  }


?>
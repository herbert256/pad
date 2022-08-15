<?php

  function pCache_init ($url, $etag) {
    $GLOBALS ['padCache_mem'] = new Memcached();
    $GLOBALS ['padCache_mem']->addServer($GLOBALS ['padCache_memory_host'], $GLOBALS ['padCache_memory_port']);
  }

  function pCache_etag ($get) {
    return $GLOBALS ['padCache_mem']->get($get);
  }

  function pCache_url ($url) {
    return $GLOBALS ['padCache_mem']->get($url);
  }

  function pCache_get ($etag) {
    return $GLOBALS ['padCache_mem']->get("x$etag");
  }


  function pCache_store ($url, $etag, $data) {

    $GLOBALS ['padCache_mem']->set($etag, $_SERVER['REQUEST_TIME'], $GLOBALS ['padCache_server_age']);

    if ( ! $GLOBALS ['padCache_server_no_data'] ) {
      $GLOBALS ['padCache_mem']->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $GLOBALS ['padCache_server_age']);
      $GLOBALS ['padCache_mem']->set("x$etag", $data,                          $GLOBALS ['padCache_server_age']+10);
     }
    
  }

  
  function pCache_update ($url, $etag) {

    $GLOBALS ['padCache_mem']->set($etag, $_SERVER['REQUEST_TIME'], $GLOBALS ['padCache_server_age']);

    if ( ! $GLOBALS ['padCache_server_no_data'] ) {
      $GLOBALS ['padCache_mem']->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $GLOBALS ['padCache_server_age']);
      $GLOBALS ['padCache_mem']->touch("x$etag", $GLOBALS ['padCache_server_age']+10);
    }

  }


  function pCache_delete ($url, $etag) {

    $GLOBALS ['padCache_mem']->delete($etag);
 
    if ( ! $GLOBALS ['padCache_server_no_data'] )
      $GLOBALS ['padCache_mem']->delete("x$etag");

  }


?>
<?php

  // Database backend for the page cache: table 'etag' holds the age per ETag, 'url' maps
  // the hashed request URI to its ETag and age, and 'data' holds the bodies.
  //
  // Implements the padCacheInit/Etag/Url/Get/Store/Update/Delete interface that
  // cache/inits.php and cache/exits.php call. padCacheInit opens its own connection from
  // the $padCacheDb* settings - the cache never shares the application connection - and
  // padCacheDb runs every statement over it in PAD's own short SQL dialect.
  //
  // With $padCacheServerNoData only the etag table is maintained, so the cache answers
  // 304 from a client ETag but has no url or data rows to serve a body from.

  function padCacheInit ($url, $etag) {

    global $padCacheDbConnect, $padCacheDbHost, $padCacheDbUser, $padCacheDbPassword, $padCacheDbDatabase;

    $padCacheDbConnect = padDbConnect ( $padCacheDbHost, $padCacheDbUser, $padCacheDbPassword, $padCacheDbDatabase );

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

    global $padCacheServerNoData;

    padCacheDb ( "replace etag values ('{0}', {1})", [$etag,$_SERVER['REQUEST_TIME']] );

    if ( ! $padCacheServerNoData ) {
      padCacheDb ( "replace url  values ('{0}', {1}, '{2}')", [$url,$_SERVER['REQUEST_TIME'],$etag] );
      padCacheDb ( "replace data values ('{0}', '{1}'     )", [$etag,$data] );
    }

  }

  function padCacheUpdate ($url, $etag) {

    global $padCacheServerNoData;

    padCacheDb ( "update etag set age={0} where etag='{1}'", [$_SERVER['REQUEST_TIME'],$etag] );

    if ( ! $padCacheServerNoData )
      padCacheDb ( "update url set age={0} where url='{1}'", [$_SERVER['REQUEST_TIME'],$url] );

  }

  function padCacheDelete ($url, $etag) {

    global $padCacheServerNoData;

    padCacheDb ( "delete from etag where etag='{0}'", [$etag] );

    if ( ! $padCacheServerNoData )
      padCacheDb ( "delete from data where etag='{0}'", [$etag] );

  }

  function padCacheDb ( $sql, $vars=[] ) {

    global $padCacheDbConnect;

    return padDbPart2 ( $padCacheDbConnect, $sql, $vars, 'cache' );

  }

  // The tables keep what they are given, so once per age window - a marker file under
  // DATA/cache/ says when the last sweep ran - the stale rows are deleted, and the bodies
  // no ETag row points at any more.

  function padCachePurge ( $before ) {

    $marker = DATA . 'cache/db-purged';

    if ( file_exists ( $marker ) and filemtime ( $marker ) >= $before )
      return;

    padFilePut ( $marker, '' );
    @touch ( $marker, $_SERVER['REQUEST_TIME'] );

    padCacheDb ( "delete from url  where age < {0}", [ $before ] );
    padCacheDb ( "delete from etag where age < {0}", [ $before ] );
    padCacheDb ( "delete from data where etag not in ( select etag from etag )" );

  }

?>
<?php


  /**
   * Initializes the file-based cache backend.
   *
   * No-op for file backend as no connection is needed.
   *
   * @param string $url  The URL being cached (unused).
   * @param string $etag The ETag value (unused).
   *
   * @return void
   */
  function padCacheInit ($url, $etag) {}


  /**
   * Retrieves the cache modification time for a given ETag.
   *
   * @param string $etag The ETag to look up.
   *
   * @return int|false The file modification timestamp, or FALSE if not found.
   */
  function padCacheEtag ($etag) {

    return ( padCacheExists ("etag/$etag") ) ? padCacheTime ("etag/$etag") : FALSE;

  }

  
  /**
   * Retrieves cache metadata for a URL from the file system.
   *
   * Looks up the URL file to get the associated ETag, then retrieves
   * the cache time for that ETag.
   *
   * @param string $url The URL to look up.
   *
   * @return array Array with [time, etag] if found, empty array otherwise.
   */
  function padCacheUrl ($url) {

    if ( padCacheExists ("url/$url") ) {
      $etag = padFileGet ("url/$url");
      if ( padCacheExists ("etag/$etag") )
        return [padCacheTime ("etag/$etag"), $etag];
    }

    return [];

  }


  /**
   * Retrieves cached data for a given ETag from the file system.
   *
   * @param string $etag The ETag identifying the cached content.
   *
   * @return string|false The cached data, or FALSE if not found.
   */
  function padCacheGet ($etag) {

    return ( padCacheExists ("etag/$etag" ) ) ? padFileGet ("etag/$etag") : FALSE;

  }


  /**
   * Stores content in the file-based cache.
   *
   * Saves the URL-to-ETag mapping and either touches the ETag file
   * (if NoData mode) or stores the actual data content.
   *
   * @param string $url  The URL being cached.
   * @param string $etag The ETag identifier for the content.
   * @param string $data The content data to cache.
   *
   * @return void
   */
  function padCacheStore ($url, $etag, $data) {

    padFilePut ("url/$url", $etag);

    if ( $GLOBALS ['padCacheServerNoData'] )
      padCacheTouch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      padFilePut ("etag/$etag", $data);

  }

  
  /**
   * Updates the cache timestamp for an ETag file.
   *
   * Touches the ETag file to refresh its modification time.
   *
   * @param string $url  The URL being refreshed (unused in file backend).
   * @param string $etag The ETag identifier to update.
   *
   * @return void
   */
  function padCacheUpdate ($url, $etag) {

    padCacheTouch ("etag/$etag", $_SERVER['REQUEST_TIME']);

  }


  /**
   * Deletes cached files for a URL and ETag.
   *
   * Removes both the URL mapping file and the ETag data file.
   *
   * @param string $url  The URL file to delete.
   * @param string $etag The ETag file to delete.
   *
   * @return void
   */
  function padCacheDelete ($url, $etag) {

    padCacheDeleteFile ("url/$url");
    padCacheDeleteFile ("etag/$etag");

  }


  /**
   * Checks if a cache file exists.
   *
   * @param string $file The relative cache file path.
   *
   * @return bool TRUE if file exists, FALSE otherwise.
   *
   * @global string $padCacheFile Base cache directory path.
   */
  function  padCacheExists ( $file ) {

    $file = $GLOBALS ['padCacheFile'] . $file;

    $return = file_exists ($file);

    return $return;

  }


  /**
   * Touches a cache file with a specific modification time.
   *
   * Creates parent directories if needed, then touches the file.
   *
   * @param string $file The relative cache file path.
   * @param int    $time The Unix timestamp to set as modification time.
   *
   * @return void
   *
   * @global string $padCacheFile Base cache directory path.
   */
  function padCacheTouch ($file, $time) {

    padCacheChkDir ($file);

    $file = $GLOBALS ['padCacheFile'] . $file;

    touch ( $file, $time );

  }
  

  /**
   * Ensures the parent directory for a cache file exists.
   *
   * Creates the directory recursively if it doesn't exist.
   *
   * @param string $file The relative cache file path.
   *
   * @return void
   *
   * @global string $padCacheFile Base cache directory path.
   * @global int    $padDirMode   Directory creation permission mode.
   */
  function padCacheChkDir ($file) {

    $file = $GLOBALS ['padCacheFile'] . $file;
    $dir  = substr($file, 0, strrpos($file, '/'));

    if ( ! file_exists ($dir) )
      mkdir($dir, $GLOBALS ['padDirMode'], true);

  }

  /**
   * Deletes a cache file if it exists.
   *
   * @param string $file The relative cache file path to delete.
   *
   * @return void
   *
   * @global string $padCacheFile Base cache directory path.
   */
  function padCacheDeleteFile ($file) {

    $file = $GLOBALS ['padCacheFile'] . $file;

    if ( file_exists($file) )
      unlink ($file);

  }


  /**
   * Gets the modification time of a cache file.
   *
   * @param string $file The relative cache file path.
   *
   * @return int The file modification timestamp, or 0 if not found.
   *
   * @global string $padCacheFile Base cache directory path.
   */
  function padCacheTime ($file) {

    $file = $GLOBALS ['padCacheFile'] . $file;

    if ( file_exists($file) )
      return filemtime($file);

    return 0;

  }


?>
 <?php
  
  //  Error handling

  $pError_action = 'pad';  // 'pad'    = PAD's own full blown error handler.
                              // 'boot'   = Use the lightweight PAD boot error handler
                              // 'php'    = Use the PHP defaults (php.ini).
                              // 'stop'   = Stop processing but do the PAD exit handling.
                              // 'abort'  = Abort, don't do the PAD exit handling
                              // 'ignore' = Ignore all errors and continue processing.
                              // 'report' = Report the error and continue processing.
 
  $pError_level  = 'all';  // Kind of errors that will be processed by $pError_action
                              // 'none' , 'error' , 'warning' , 'notice' , 'all'
                              // (not used when $pError_action is 'php' or 'boot')

  $pError_log    = TRUE;  //  Write errors to Apache error log
  $pError_dump   = TRUE;   //  Dump errors to the DATA directory

  // Trace the internal working of PAD

  $pTrace = FALSE;  

  // Keep track of stuff

  $pTrack_file_request = FALSE;
  $pTrack_file_data    = FALSE; 

  $pTrack_db_session  = FALSE;
  $pTrack_db_request  = FALSE;
  $pTrack_db_data     = FALSE;
  
  $pTrack_sql         = FALSE;   //  Detail information about every executed SQL statement.

  // Cache settings
  
  $pCache_server_age       = 0;                    //  Seconds to keep the cache at PAD server side, 
                                                      //  0 to turn of server-side caching

  $pCache_proxy_age        = 0;                    //  How long a proxy is allowed to cache. 
                                                      //  0 to turn of proxy-side caching

  $pCache_client_age       = 0;                    //  How long the client is allowed to cache.
                                                      //  0 to turn of client-side caching

  // Server-side cache settings ( used wheb $pCache_server_age <> 0 )

  $pCache_server_type      = 'memory';             //  The implementation of the server-side cache: file/db/memory
  $pCache_server_gzip      = FALSE;                //  Store the cache zipped
  $pCache_server_no_data   = FALSE;                //  Do not store the data itself, only the etag and timestamp,
                                                      //  caching based on the client 'etag' & 'modified' HTTP headers.

  $pCache_memory_host      = 'localhost';          //  Used when $pCache_server_type is 'memory'
  $pCache_memory_port      = '11211';

  $pCache_db_host          = 'localhost';          //  Used when $pCache_server_type is 'db'
  $pCache_db_database      = 'cache';
  $pCache_db_user          = 'cache';
  $pCache_db_password      = 'cache';

  $pCache_file             = DATA . 'cache/';   //  Used when $pCache_server_type is 'file'
  $pCache_file_mode        = 755;

  // SQL parms - PAD internal

  $pPad_sql_host           = '127.0.0.1';
  $pPad_sql_database       = 'pad';
  $pPad_sql_user           = 'pad';
  $pPad_sql_password       = 'pad';

  // SQL parms - application

  $pSql_host               = '127.0.0.1';
  $pSql_database           = $app;
  $pSql_user               = $app;
  $pSql_password           = $app;

  // If pad creates a directory or file.

  $pDir_mode  = 0755;
  $pFile_mode = 0755;

  // Default date/time formating
  
  $pFmt_date      = 'Y-m-d';
  $pFmt_time      = 'H:i:s';
  $pFmt_timestamp = 'Y-m-d H:i:s';
  
  // Keep track of vars in a session.
  
  $pSession_vars = [];

  // How the app parts from ../$app/pages/ are processed.

  $pBuild_mode     = 'before';     // 'isolate'
                                      // 'before'
                                      // 'demand'
                                      // 'include'
  
  $pBuild_merge    = 'content';    // 'content'
                                      // 'end'
 
  // PAD database structure
  
  $pDbTables = $pDb_relations = [];
    
  // Default {$var} options, there must be a PHP snippet in one of below directories
  // - PAD/functions/
  // - APP/functions/

  $pData_default_start = ['trim', 'white'];
  $pData_default_end   = ['html', 'nbsp'];

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php
  
  $pSanitize = ['STRIP_LOW', 'ENCODE_HIGH'];

  // lib tidy

  $pTidy                   = FALSE;
  $pTidy_ccsid             = 'utf8'; 
  $pTidy_config            = [ 
                                  'output-html'     => FALSE,
                                  'doctype'         => 'html5',
                                  'wrap'            => 200,
                                  'indent'          => FALSE,
                                  'tab-size'        => 2,
                                  'vertical-space'  => 'yes',
                                  'replace-color'   => 'yes'
                                ];

  $pRemove_whitespace      = FALSE;

  $pLocal = [ 'localhost', 'penguin.linux.test', '127.0.0.1' ];
  
  // Other settings.

  $pClient_gzip            = FALSE; // Send the result zipped
  $pEtag_304               = FALSE;  // Send a 304 header, based on the client etag http header
  $pNo_no                  = FALSE; // No PAD stuff, just plane PHP   
  $pFast_link              = 32;
  $pTiming                 = TRUE;

?>
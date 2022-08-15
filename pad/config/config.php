 <?php
  
  //  Error handling

  $padError_action = 'pad';  // 'pad'    = PAD's own full blown error handler.
                              // 'boot'   = Use the lightweight PAD boot error handler
                              // 'php'    = Use the PHP defaults (php.ini).
                              // 'stop'   = Stop processing but do the PAD exit handling.
                              // 'abort'  = Abort, don't do the PAD exit handling
                              // 'ignore' = Ignore all errors and continue processing.
                              // 'report' = Report the error and continue processing.
 
  $padError_level  = 'all';  // Kind of errors that will be processed by $padError_action
                              // 'none' , 'error' , 'warning' , 'notice' , 'all'
                              // (not used when $padError_action is 'php' or 'boot')

  $padError_log    = TRUE;  //  Write errors to Apache error log
  $padError_dump   = TRUE;   //  Dump errors to the DATA directory

  // Trace the internal working of PAD

  $padTrace = FALSE;  

  // Keep track of stuff

  $padTrack_file_request = FALSE;
  $padTrack_file_data    = FALSE; 

  $padTrack_db_session  = FALSE;
  $padTrack_db_request  = FALSE;
  $padTrack_db_data     = FALSE;
  
  $padTrack_sql         = FALSE;   //  Detail information about every executed SQL statement.

  // Cache settings
  
  $padCache_server_age       = 0;                    //  Seconds to keep the cache at PAD server side, 
                                                      //  0 to turn of server-side caching

  $padCache_proxy_age        = 0;                    //  How long a proxy is allowed to cache. 
                                                      //  0 to turn of proxy-side caching

  $padCache_client_age       = 0;                    //  How long the client is allowed to cache.
                                                      //  0 to turn of client-side caching

  // Server-side cache settings ( used wheb $padCache_server_age <> 0 )

  $padCache_server_type      = 'memory';             //  The implementation of the server-side cache: file/db/memory
  $padCache_server_gzip      = FALSE;                //  Store the cache zipped
  $padCache_server_no_data   = FALSE;                //  Do not store the data itself, only the etag and timestamp,
                                                      //  caching based on the client 'etag' & 'modified' HTTP headers.

  $padCache_memory_host      = 'localhost';          //  Used when $padCache_server_type is 'memory'
  $padCache_memory_port      = '11211';

  $padCache_db_host          = 'localhost';          //  Used when $padCache_server_type is 'db'
  $padCache_db_database      = 'cache';
  $padCache_db_user          = 'cache';
  $padCache_db_password      = 'cache';

  $padCache_file             = DATA . 'cache/';   //  Used when $padCache_server_type is 'file'
  $padCache_file_mode        = 755;

  // SQL parms - PAD internal

  $padPad_sql_host           = '127.0.0.1';
  $padPad_sql_database       = 'pad';
  $padPad_sql_user           = 'pad';
  $padPad_sql_password       = 'pad';

  // SQL parms - application

  $padSql_host               = '127.0.0.1';
  $padSql_database           = $app;
  $padSql_user               = $app;
  $padSql_password           = $app;

  // If pad creates a directory or file.

  $padDir_mode  = 0755;
  $padFile_mode = 0755;

  // Default date/time formating
  
  $padFmt_date      = 'Y-m-d';
  $padFmt_time      = 'H:i:s';
  $padFmt_timestamp = 'Y-m-d H:i:s';
  
  // Keep track of vars in a session.
  
  $padSession_vars = [];

  // How the app parts from ../$app/pages/ are processed.

  $padBuild_mode     = 'before';     // 'isolate'
                                      // 'before'
                                      // 'demand'
                                      // 'include'
  
  $padBuild_merge    = 'content';    // 'content'
                                      // 'end'
    
  // Default {$var} options, there must be a PHP snippet in one of below directories
  // - PAD/functions/
  // - APP/functions/

  $padData_default_start = ['trim', 'white'];
  $padData_default_end   = ['html', 'nbsp'];

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php
  
  $padSanitize = ['STRIP_LOW', 'ENCODE_HIGH'];

  // lib tidy

  $padTidy                   = FALSE;
  $padTidy_ccsid             = 'utf8'; 
  $padTidy_config            = [ 
                                  'output-html'     => FALSE,
                                  'doctype'         => 'html5',
                                  'wrap'            => 200,
                                  'indent'          => FALSE,
                                  'tab-size'        => 2,
                                  'vertical-space'  => 'yes',
                                  'replace-color'   => 'yes'
                                ];

  $padRemove_whitespace      = FALSE;

  $padLocal = [ 'localhost', 'penguin.linux.test', '127.0.0.1' ];
  
  // Other settings.

  $padClient_gzip            = FALSE; // Send the result zipped
  $padEtag_304               = FALSE;  // Send a 304 header, based on the client etag http header
  $padNo_no                  = FALSE; // No PAD stuff, just plane PHP   
  $padFast_link              = 32;
  $padTiming                 = TRUE;

?>
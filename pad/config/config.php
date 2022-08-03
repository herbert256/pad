 <?php
  
  //  Error handling

  $pad_error_action = 'pad';  // 'pad'    = PAD's own full blown error handler.
                              // 'boot'   = Use the lightweight PAD boot error handler
                              // 'php'    = Use the PHP defaults (php.ini).
                              // 'stop'   = Stop processing but do the PAD exit handling.
                              // 'abort'  = Abort, don't do the PAD exit handling
                              // 'ignore' = Ignore all errors and continue processing.
                              // 'report' = Report the error and continue processing.
 
  $pad_error_level  = 'all';  // Kind of errors that will be processed by $pad_error_action
                              // 'none' , 'error' , 'warning' , 'notice' , 'all'
                              // (not used when $pad_error_action is 'php' or 'boot')

  $pad_error_log    = TRUE;  //  Write errors to Apache error log
  $pad_error_dump   = TRUE;   //  Dump errors to the DATA directory

  // Trace the internal working of PAD

  $pad_trace           = FALSE;  
  $pad_trace_level     = FALSE;  
  $pad_trace_errors    = FALSE;  
  $pad_trace_occurence = FALSE;  
  $pad_trace_fields    = FALSE;  
  $pad_trace_eval      = FALSE;   
  $pad_trace_curl      = FALSE;  
  $pad_trace_cache     = FALSE;  
  $pad_trace_timings   = FALSE;  
  $pad_trace_headers   = FALSE;  
  $pad_trace_options   = FALSE;
  $pad_trace_sql       = FALSE;
  $pad_trace_explode   = FALSE;
  $pad_trace_tag       = FALSE;

  // Keep track of stuff

  $pad_track_file_request = FALSE;
  $pad_track_file_data    = FALSE; 

  $pad_track_db_session  = FALSE;
  $pad_track_db_request  = FALSE;
  $pad_track_db_data     = FALSE;
  
  $pad_track_sql         = FALSE;   //  Detail information about every executed SQL statement.

  // Cache settings
  
  $pad_cache_server_age       = 0;                    //  Seconds to keep the cache at PAD server side, 
                                                      //  0 to turn of server-side caching

  $pad_cache_proxy_age        = 0;                    //  How long a proxy is allowed to cache. 
                                                      //  0 to turn of proxy-side caching

  $pad_cache_client_age       = 0;                    //  How long the client is allowed to cache.
                                                      //  0 to turn of client-side caching

  // Server-side cache settings ( used wheb $pad_cache_server_age <> 0 )

  $pad_cache_server_type      = 'memory';             //  The implementation of the server-side cache: file/db/memory
  $pad_cache_server_gzip      = FALSE;                //  Store the cache zipped
  $pad_cache_server_no_data   = FALSE;                //  Do not store the data itself, only the etag and timestamp,
                                                      //  caching based on the client 'etag' & 'modified' HTTP headers.

  $pad_cache_memory_host      = 'localhost';          //  Used when $pad_cache_server_type is 'memory'
  $pad_cache_memory_port      = '11211';

  $pad_cache_db_host          = 'localhost';          //  Used when $pad_cache_server_type is 'db'
  $pad_cache_db_database      = 'cache';
  $pad_cache_db_user          = 'cache';
  $pad_cache_db_password      = 'cache';

  $pad_cache_file             = DATA . 'cache/';   //  Used when $pad_cache_server_type is 'file'
  $pad_cache_file_mode        = 755;

  // SQL parms - PAD internal

  $pad_pad_sql_host           = '127.0.0.1';
  $pad_pad_sql_database       = 'pad';
  $pad_pad_sql_user           = 'pad';
  $pad_pad_sql_password       = 'pad';

  // SQL parms - application

  $pad_sql_host               = '127.0.0.1';
  $pad_sql_database           = $app;
  $pad_sql_user               = $app;
  $pad_sql_password           = $app;

  // If pad creates a directory or file.

  $pad_dir_mode  = 0755;
  $pad_file_mode = 0755;

  // Default date/time formating
  
  $pad_fmt_date      = 'Y-m-d';
  $pad_fmt_time      = 'H:i:s';
  $pad_fmt_timestamp = 'Y-m-d H:i:s';
  
  // Keep track of vars in a session.
  
  $pad_session_vars = [];

  // How the app parts from ../$app/pages/ are processed.

  $pad_build_mode     = 'before';     // 'isolate'
                                      // 'before'
                                      // 'demand'
                                      // 'include'
  
  $pad_build_merge    = 'content';    // 'content'
                                      // 'end'
 
  // PAD database structure
  
  $pad_db_tables = $pad_db_relations = [];
    
  // Default {$var} options, there must be a PHP snippet in one of below directories
  // - PAD/functions/
  // - APP/functions/

  $pad_data_default_start = ['trim', 'white'];
  $pad_data_default_end   = ['html', 'nbsp'];

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php
  
  $pad_sanitize = ['STRIP_LOW', 'ENCODE_HIGH'];

  // lib tidy

  $pad_tidy                   = FALSE;
  $pad_tidy_ccsid             = 'utf8'; 
  $pad_tidy_config            = [ 
                                  'output-html'     => FALSE,
                                  'doctype'         => 'html5',
                                  'wrap'            => 200,
                                  'indent'          => FALSE,
                                  'tab-size'        => 2,
                                  'vertical-space'  => 'yes',
                                  'replace-color'   => 'yes'
                                ];

  $pad_remove_whitespace      = FALSE;

  $pad_local = [ 'localhost', 'penguin.linux.test', '127.0.0.1' ];
  
  // Other settings.

  $pad_client_gzip            = FALSE; // Send the result zipped
  $pad_etag_304               = FALSE;  // Send a 304 header, based on the client etag http header
  $pad_no_no                  = FALSE; // No PAD stuff, just plane PHP   
  $pad_fast_link              = 32;
  $pad_timing                 = TRUE;

?>
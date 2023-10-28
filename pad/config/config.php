<?php
  
  //  Error handling

  $padErrorAction = 'pad';  // 'pad'    = PAD's own full blown error handler.
                            // 'boot'   = Use the lightweight PAD boot error handler
                            // 'php'    = Use the PHP defaults (php.ini).
                            // 'stop'   = Stop processing but do the PAD stop handling.
                            // 'exit'   = Exit, don't do the PAD stop handling
                            // 'ignore' = Ignore all errors and continue processing.
                            // 'report' = Report the error and continue processing.
 
  $padErrorLevel  = 'all';  // Kind of PHP errors that will be processed by $padErrorAction
                            // 'none' , 'error' , 'warning' , 'notice' , 'all'
                            // (not used when $padErrorAction is 'php' or 'boot')

  $padErrorLog    = TRUE;   //  Report errors to Apache error log
  $padErrorReport = TRUE;   //  Report errors to the DATA directory

  // Keep track of stuff

  $padTrackFileRequest = FALSE;
  $padTrackFileData    = FALSE; 

  $padTrackDbSession   = TRUE;
  $padTrackDbRequest   = TRUE;
  $padTrackDbData      = TRUE;

   // Trace the working of PAD

  $padTrace = TRUE;

  $padTraceTypes ['global']   = TRUE;

  $padTraceTypes ['nested']   = TRUE;
  $padTraceTypes ['local']    = TRUE;

  $padTraceTypes ['store']    = TRUE;
  $padTraceTypes ['sequence'] = TRUE;
  $padTraceTypes ['data']     = TRUE;
  $padTraceTypes ['content']  = TRUE;
  $padTraceTypes ['base']     = TRUE;
  $padTraceTypes ['result']   = TRUE;

  $padTraceTypes ['occur']    = TRUE;
  $padTraceTypes ['xml']      = TRUE;

  $padTraceTypes ['start']    = TRUE;
  $padTraceTypes ['end']      = TRUE;
  $padTraceTypes ['flags']    = TRUE;
  $padTraceTypes ['true']     = TRUE;
  $padTraceTypes ['false']    = TRUE;

  $padTraceTypes ['build']    = TRUE;
  $padTraceTypes ['parse']    = TRUE;
  $padTraceTypes ['var']      = TRUE;
  $padTraceTypes ['field']    = TRUE;
  $padTraceTypes ['eval']     = TRUE;
  $padTraceTypes ['parms']    = TRUE;
  $padTraceTypes ['call']     = TRUE;
  $padTraceTypes ['exists']   = TRUE;
  $padTraceTypes ['sql']      = TRUE;
  $padTraceTypes ['curl']     = TRUE;
  $padTraceTypes ['dumps']    = TRUE;

  $padTraceTypes ['types-global'] = TRUE;
  $padTraceTypes ['types-local']  = TRUE;
      
  // Cache settings
  
  $padCacheServerAge       = 0;                    //  Seconds to keep the cache at pad server side, 
                                                   //  0 to turn of server-side caching

  $padCacheProxyAge        = 0;                    //  How long a proxy is allowed to cache. 
                                                   //  0 to turn of proxy-side caching

  $padCacheClientAge       = 0;                    //  How long the client is allowed to cache.
                                                   //  0 to turn of client-side caching

  // Server-side cache settings ( used when $padCacheServerAge <> 0 )

  $padCacheServerType      = 'memory';             //  The implementation of the server-side cache: file/db/memory
  $padCacheServerGzip      = TRUE;                 //  Store the cache zipped
  $padCacheServerNoData    = FALSE;                //  Do not store the data itself, only the etag and timestamp,
                                                   //  caching based on the client 'etag' & 'modified' HTTP headers.

  $padCacheMemoryHost      = 'localhost';          //  Used when $padCacheServerType is 'memory'
  $padCacheMemoryPort      = '11211';

  $padCacheDbHost          = 'localhost';          //  Used when $padCacheServerType is 'db'
  $padCacheDbDatabase      = 'cache';
  $padCacheDbUser          = 'cache';
  $padCacheDbPassword      = 'cache';

  $padCacheFile            = padData . 'cache/';      //  Used when $padCacheServerType is 'file'
  $padCacheFileMode        = 755;

  // SQL parms - pad internal

  $padPadSqlHost           = '127.0.0.1';
  $padPadSqlDatabase       = 'pad';
  $padPadSqlUser           = 'pad';
  $padPadSqlPassword       = 'pad';

  // SQL parms - application

  $padSqlHost               = '127.0.0.1';
  $padSqlDatabase           = 'app';
  $padSqlUser               = 'app';
  $padSqlPassword           = 'app';

  // If pad creates a directory or file.

  $padDirMode  = 0755;
  $padFileMode = 0755;

  // Default date/time formating
  
  $padFmtDate      = 'Y-m-d';
  $padFmtTime      = 'H:i:s';
  $padFmtTimestamp = 'Y-m-d H:i:s';
  
  // Keep track of vars in a session.
  
  $padSessionVars = [];

    
  // Default {$var} options, there must be a PHP snippet in one of below directories
  // - pad/functions/
  // - padApp/functions/

  $padDataDefaultStart = ['trim', 'white'];
  $padDataDefaultEnd   = ['html', 'nbsp'];

  $padEvalFast = ['trim', 'white', 'html', 'nbsp'];

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php

  $padOutputSanitize        = [ 'STRIP_LOW', 'ENCODE_HIGH' ];
  
  // lib tidy

  $padTidy                  = FALSE;
  $padTidyCcsid             = 'utf8'; 
  $padTidyConfig            = [ 
                                  'output-pad'     => TRUE,
                                  'doctype'         => 'html5',
                                  'wrap'            => 0,
                                  'indent'          => TRUE,
                                  'tab-size'        => 2,
                                  'vertical-space'  => 'yes',
                                  'replace-color'   => 'yes',
                                  'markup'          => 'yes',
                                  'omit-optional-tags' => 'yes',
                                  'clean'              => 'yes',
                                  'drop-empty-elements' => 'yes',
                                  'merge-spans' => 'yes',
                                  'merge-divs' => 'yes'
                              ];

  // Basic formatting
                                
  $padOutputTabToSpace       = TRUE;
  $padOutputTrim             = TRUE;
  $padOutputRemoveWhitespace = FALSE;
  $padOutputNoEmptyLines     = TRUE;
  $padOutputNoIndent         = TRUE;
  $padOutputNoNewLines       = FALSE;
  
  // Other settings.

  $padClientGzip            = FALSE;  // Send the result zipped
  $padEtag304               = TRUE;   // Send a 304 header, based on the client etag http header
  $padNoNo                  = FALSE;  // No pad stuff, just plane PHP   
  $padFastLink              = 32;     // Lenght of the FastLink code in the URL

  //No paramenter parsing for below tags:

  $padPrmNoParse            = [ 'if', 'case', 'while', 'until', 'increment', 'decrement' ];

  $padTables    = [];
  $padRelations = [];

?>
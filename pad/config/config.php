<?php
  
  //  Error handling

  $padErrorAction = 'pad';  // 'pad'    = PAD's own full blown error handler.
                            // 'boot'   = Keep using the lightweight PAD boot error handler
                            // 'php'    = Use the PHP defaults (php.ini).
                            // 'stop'   = Stop processing but do the PAD stop handling.
                            // 'exit'   = Exit, don't do the PAD stop handling
                            // 'ignore' = Ignore all errors and continue processing.
                            // 'report' = Report errors and continue processing.
 
  $padErrorLevel  = 'all';  // Kind of PHP errors that will be processed by $padErrorAction
                            // 'none' , 'error' , 'warning' , 'notice' , 'all'
                            // (not used when $padErrorAction is 'php' or 'boot')

  $padErrorLog    = TRUE;   //  Report errors to Apache error log
  $padErrorReport = TRUE;   //  Report errors to the DATA directory

  // Tail:  tail/track/trace/xref etc
  // If set then the config file with the same name will be load from the sub folder tail 

  $padTail = '';

  // Where the output goes
  // A config file with the same name will be load from the sub folder output

  $padOutputType = 'web';   // web/file/ftp/email/download

  // Cache settings
  
  $padCacheServerAge = 0;   //  Seconds to keep the cache at pad server side, 
                            //  0 to turn of server-side caching

  $padCacheProxyAge  = 0;   //  How long a proxy is allowed to cache. 
                            //  0 to turn of proxy-side caching

  $padCacheClientAge = 0;   //  How long the client is allowed to cache.
                            //  0 to turn of client-side caching

  // Server-side cache settings ( used when $padCacheServerAge <> 0 )

  $padCacheServerType      = 'memory';            //  The implementation of the server-side cache: file/db/memory
  $padCacheServerGzip      = TRUE;                //  Store the cache zipped
  $padCacheServerNoData    = FALSE;               //  Do not store the data itself, only the etag and timestamp,
                                                  //  caching based on the client 'etag' & 'modified' HTTP headers.

  $padCacheMemoryHost      = 'localhost';         //  Used when $padCacheServerType is 'memory'
  $padCacheMemoryPort      = '11211';

  $padCacheDbHost          = 'localhost';         //  Used when $padCacheServerType is 'db'
  $padCacheDbDatabase      = 'cache';
  $padCacheDbUser          = 'cache';
  $padCacheDbPassword      = 'cache';

  $padCacheFile            = padData . 'cache/';  //  Used when $padCacheServerType is 'file'
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
  $padFileMode = 0644;

  // Default date/time formating
  
  $padFmtDate      = 'Y-m-d';
  $padFmtTime      = 'H:i:s';
  $padFmtTimestamp = 'Y-m-d H:i:s';
  
  // Keep track of vars in a session.
  
  $padSessionVars = [];
    
  // Default {$var} options, there must be a PHP snippet in one of below directories
  // - pad/_functions/
  // - padApp/_functions/

  $padDataDefaultStart = ['trim', 'white'];
  $padDataDefaultEnd   = ['html', 'nbsp'];

  // lib tidy

  $padTidy       = FALSE;
  $padTidyCcsid  = 'utf8'; 
  $padTidyConfig = [ 
    'output-html'         => TRUE,
    'doctype'             => 'loose',
    'wrap'                => 0,
    'indent'              => TRUE,
    'tab-size'            => 2,
    'vertical-space'      => 'no',
    'indent-spaces'       => 2,
    'replace-color'       => 'yes',
    'markup'              => 'yes',
    'omit-optional-tags'  => 'yes',
    'clean'               => 'yes',
    'drop-empty-elements' => 'yes',
    'merge-spans'         => 'yes',
    'force-output'        => true,
    'show-warnings'       => FALSE,
    'merge-divs'          => 'yes'
  ];

  // myTidy
  // A basic & buggy implementation of formatting the output HTML 
  // Only used when $padTidy is FALSE

  $padMyTidy                 = FALSE;
  $padMyTidySanitize         = [ 'STRIP_LOW', 'ENCODE_HIGH' ];
  $padMyTidyTabToSpace       = TRUE;
  $padMyTidyTrim             = TRUE;
  $padMyTidyRemoveWhitespace = FALSE;  
  $padMyTidyNoIndent         = TRUE;
  $padMyTidyNoEmptyLines     = TRUE;
  $padMyTidyNoNewLines       = FALSE;
  
  // Other settings.

  $padClientGzip            = FALSE;  // Send the result zipped
  $padNoNo                  = FALSE;  // No pad stuff, just plane PHP   
  $padFastLink              = 32;     // Lenght of the FastLink code in the URL

  $padTables    = [];
  $padRelations = [];

?>
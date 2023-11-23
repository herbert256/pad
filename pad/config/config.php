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

  // The working of PAD and Big Brother
  // If one or more are set to TRUE then the config file tail.php will be read. 

  $padRequest = FALSE;    // Log the details of the HTTP(s) request 
  $padTrace   = FALSE;    // Trace the internal working of PAD
  $padTrack   = FALSE;    // Big Brother, session and request information of the client
  $padXml     = TRUE;     // Build a XML file of the structure of the PAD page
  $padXref    = TRUE;     // Build the <app>_xref and <data>xref directories

  // Cache settings
  
  $padCache = FALSE;

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

  $padEvalFast = ['trim', 'white', 'html', 'nbsp'];

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php

  $padOutputSanitize        = [ 'STRIP_LOW', 'ENCODE_HIGH' ];
  
  // lib tidy

  $padTidy                  = FALSE;

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

  //No parameter parsing for below tags:

  $padPrmNoParse = [ 'if', 'case', 'while', 'until', 'increment', 'decrement' ];

  $padTables    = [];
  $padRelations = [];

?>
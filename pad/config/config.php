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

  // info/track/trace/xref etc
  // If set then a config file with the same name will be load from the sub folder info 

  $padInfo = '';

  // Where the output goes
  // A config file with the same name will be load from the sub folder output

  $padOutputType = 'web'; // web/file/download

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
  // - pad/functions/
  // - padApp/_functions/

  $padDataDefaultStart = [];
  $padDataDefaultEnd   = ['html'];

  // Formatting the output

  $padTidy   = FALSE;
  $padMyTidy = FALSE;

  $padTables    = [];
  $padRelations = [];

    // Other settings.

  $padGzip      = FALSE;  // Send the result zipped
  $padCookies   = TRUE;  // Send the result zipped
  $padNoNo      = FALSE;  // No pad stuff, just plane PHP   
  $padFastLink  = 32;     // Lenght of the FastLink code in the URL

?>
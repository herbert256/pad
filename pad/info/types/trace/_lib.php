<?php


  /**
   * Gets the starting trace level for the current trace counter.
   *
   * @return int The trace level to start from, or 0 if not set or negative.
   */
  function padInfoTraceStart ( ) {

    if ( ! isset ( $GLOBALS ['padInfoTraceLvl'] [ $GLOBALS ['padInfoTraceCnt'] ] ) )
      return 0;

    if ( $GLOBALS ['padInfoTraceLvl'] [ $GLOBALS ['padInfoTraceCnt'] ] < 0 )
      return 0;

    return $GLOBALS ['padInfoTraceLvl'] [ $GLOBALS ['padInfoTraceCnt'] ];

  }

  /**
   * Records a trace event during PAD execution.
   *
   * Writes trace information to various outputs based on configuration:
   * root trace, tree trace, local trace, and type-specific traces.
   *
   * @param string $type  The trace type (level, occur, etc.).
   * @param string $event The event name (start, end, etc.).
   * @param string $info  Optional additional info to include.
   *
   * @return void
   *
   * @global int $pad Current processing level.
   * @global array $padOccur Occurrence counters per level.
   */
  function padInfoTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padInfoTraceMore, $padInfoTraceRoot, $padInfoTraceTree, $padInfoTraceLocal, $padInfoTraceSkipLevel;
    global $padInfoTraceId, $padInfoTraceTypes, $padInfoTraceIds, $padInfoTraceOccurId, $padInfoTraceMaxLevel;

    if ( $padInfoTraceSkipLevel and $padInfoTraceSkipLevel == $pad ) return;
    if ( $padInfoTraceMaxLevel  and $padInfoTraceMaxLevel  >  $pad ) return;

    if ( padInfoTraceSkip ( $type ) )
      return;

    $padInfoTraceId++;

    $occur = $padOccur [$pad] ?? 0;

    if ( $event == 'start' )
      if     ( $type == 'level' ) $padInfoTraceIds [$pad]         = $padInfoTraceId;
      elseif ( $type == 'occur' ) $padInfoTraceOccurId [$pad]     = $padInfoTraceId;
      else                        $GLOBALS ["padInfoTraceX$type"] = $padInfoTraceId;

    padInfoTraceInfo ( $trace, $info, $id, $type, $event );

    if ( $padInfoTraceMore  ) padInfoTraceMore  ( $trace, $info, $padInfoTraceId );
    if ( $padInfoTraceRoot  ) padInfoTraceRoot  ( $trace );
    if ( $padInfoTraceTree  ) padInfoTraceTree  ( $trace );
    if ( $padInfoTraceLocal ) padInfoTraceLocal ( $trace );
    if ( $padInfoTraceTypes ) padInfoTraceTypes ( $trace, $type );

  }


  /**
   * Writes an error to the trace ERROR directory.
   *
   * Dumps error information to a file in the trace directory
   * for later analysis.
   *
   * @param mixed $error The error to dump.
   *
   * @return void
   */
  function padInfoTraceError ( $error ) {

    if ( ! function_exists ( 'padInfoTrace') )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDir ( $error, $GLOBALS ['padInfoTraceDir'] . '/ERROR');

    } catch (Throwable $e) {

      // Ignore errors

    }

    restore_error_handler ();

  }


  /**
   * Checks if tracing should be skipped for current level.
   *
   * Returns TRUE if the current level matches skip level or
   * exceeds max level configuration.
   *
   * @param string $type The trace type (unused but passed for consistency).
   *
   * @return bool TRUE if tracing should be skipped.
   *
   * @global int $pad Current processing level.
   */
  function padInfoTraceSkip ( $type ) {

    global $pad;
    global $padInfoTraceSkipLevel, $padInfoTraceMaxLevel;

    if ( $padInfoTraceSkipLevel and $padInfoTraceSkipLevel == $pad )
      return TRUE;

    if ( $padInfoTraceMaxLevel and $padInfoTraceMaxLevel > $pad )
      return TRUE;

    return FALSE;

  }


  /**
   * Formats trace information into a structured string.
   *
   * Builds a formatted trace line with level prefix, trace ID,
   * parent ID, type, event, and truncated info.
   *
   * @param string &$trace Output: The formatted trace line.
   * @param string &$info  Input/Output: Info string (sanitized, truncated).
   * @param int    &$id    Output: The parent/reference trace ID.
   * @param string $type   The trace type.
   * @param string $event  The event name.
   *
   * @return void
   */
  function padInfoTraceInfo ( &$trace, &$info, &$id, $type, $event ) {

    global $pad, $padOccur;
    global $padInfoTraceId, $padInfoTraceIds, $padInfoTraceOccurId;

    $prefix = $pad;
    if ( $pad >= 0 and $padOccur [$pad] and $padOccur [$pad] <> 99999 )
      $prefix .= '/' . $padOccur [$pad];

    if     ( $type == 'level' )                      $id = $padInfoTraceIds [$pad]         ?? 0;
    elseif ( $type == 'occur' )                      $id = $padInfoTraceOccurId [$pad]     ?? 0;
    elseif ( isset ( $GLOBALS ["padInfoTraceX$type"] ) ) $id = $GLOBALS ["padInfoTraceX$type"] ?? 0;
    else                                             $id = $padInfoTraceId;

    if ( ! $id or $id == $padInfoTraceId )
      $id = '';

    $trace = sprintf ( '%-9s',  $prefix       )
           . sprintf ( '%-7s',  $padInfoTraceId )
           . sprintf ( '%-7s',  $id           )
           . sprintf ( '%-10s', $type         )
           . sprintf ( '%-10s', $event        );

    $info = padMakeSafe ( $info );

    if ( strlen ( $info ) > 70 )
      $trace .= substr ( $info, 0, 63 ) . ' <more>';
    else
      $trace .= $info;

  }


  /**
   * Writes extended info for truncated trace lines.
   *
   * When trace info is truncated (marked with <more>), writes
   * the full info to a separate file.
   *
   * @param string $trace The trace line to check for truncation.
   * @param string $info  The full info string.
   * @param int    $line  The trace ID to use as filename.
   *
   * @return void
   */
  function padInfoTraceMore ( $trace, $info, $line ) {

    if ( str_ends_with ( $trace, ' <more>' )  )
      padInfoTraceWrite ( -1, "more/$line.txt", $info, 'file' ) ;

  }


  /**
   * Writes trace to the root-level trace file.
   *
   * Appends the trace line to the global root.txt file.
   *
   * @param string $trace The formatted trace line.
   *
   * @return void
   */
  function padInfoTraceRoot ( $trace ) {

    padInfoTraceWrite ( -1, 'root.txt', $trace );

  }


  /**
   * Writes trace to tree files for all ancestor levels.
   *
   * Appends the trace line to tree.txt for each level from
   * the start level up to the current level.
   *
   * @param string $trace The formatted trace line.
   *
   * @return void
   *
   * @global int $pad Current processing level.
   */
  function padInfoTraceTree ( $trace ) {

    $start = padInfoTraceStart ();

    global $pad;

    for ( $i = $start; $i <= $pad; $i++ )
      padInfoTraceWrite ( $i, 'tree.txt', $trace );

  }

  /**
   * Writes trace to the current level's local trace file.
   *
   * Appends the trace line to local.txt for the current level only.
   *
   * @param string $trace The formatted trace line.
   *
   * @return void
   *
   * @global int $pad Current processing level.
   */
  function padInfoTraceLocal ( $trace ) {

    global $pad;

    padInfoTraceWrite ( $pad, 'local.txt', $trace );

  }


  /**
   * Writes trace to type-specific trace files.
   *
   * Creates separate trace files per event type (level, occur, etc.),
   * written both to current level and root level.
   *
   * @param string $trace The formatted trace line.
   * @param string $type  The trace type for filename.
   *
   * @return void
   *
   * @global int  $pad Current processing level.
   * @global bool $padInfoTraceTypesDir Use subdirectory for types.
   */
  function padInfoTraceTypes ( $trace, $type ) {

    global $pad, $padInfoTraceTypesDir;

    if ( $pad > -1 )
      if ( $padInfoTraceTypesDir )
        padInfoTraceWrite ( $pad, "/types/$type.txt", $trace );
      else
        padInfoTraceWrite ( $pad, "/types-$type.txt", $trace );

    if ( $padInfoTraceTypesDir )
      padInfoTraceWrite ( -1, "/types/$type.txt", $trace );
    else
      padInfoTraceWrite ( -1, "/types-$type.txt", $trace );

  }


  /**
   * Writes a trace line or file to the trace directory.
   *
   * Handles directory creation and path resolution for trace output.
   * Type 'line' appends to file, type 'file' creates once.
   *
   * @param int    $pad      The level (-1 for root).
   * @param string $location The relative path within trace directory.
   * @param string $trace    The content to write.
   * @param string $type     Write type: 'line' (append) or 'file' (create once).
   *
   * @return void
   */
  function padInfoTraceWrite ( $pad, $location, $trace, $type='line' ) {

    global $padOccur, $padInfoTraceDir;
    global $padInfoTraceLevel, $padInfoTraceLines, $padInfoTraceSkipLevel, $padInfoTraceMaxLevel ;

    if ( ! $padInfoTraceLines and $type == 'line' )
      return;

    if ( $padInfoTraceSkipLevel and $padInfoTraceSkipLevel == $pad ) return;
    if ( $padInfoTraceMaxLevel  and $padInfoTraceMaxLevel  >  $pad ) return;

    if ( ! isset ( $padInfoTraceLevel [$pad] ) ) padInfoTraceSet ( $pad );
    if ( ! $padInfoTraceLevel [$pad]           ) padInfoTraceSet ( $pad );

    if ( $pad < 0 )
      $add = '';
    else
      $add = $padInfoTraceLevel [$pad] . '/' . padInfoTraceOccur ( $pad );

    $target = "$padInfoTraceDir/$add$location";

    if ( $type == 'file' and file_exists ( DAT . $target ) )
      return;

    if ( $type == 'line' )
      padFilePut ( $target, $trace, 1 );
    else
      padFilePut ( $target, $trace );

  }


  /**
   * Ensures trace directory exists for line-type writes.
   *
   * Creates parent directory if needed for 'line' type writes.
   * Skips directory check for 'file' type.
   *
   * @param string $file The file path to check.
   * @param string $type The write type ('line' or 'file').
   *
   * @return void
   */
  function padInfoTraceCheckDir ( $file, $type ) {

    if ( $type == 'file' )
      return;

    padInfoChkDir ( $file );

  }


  /**
   * Gets the occurrence subdirectory path component.
   *
   * Returns subdirectory name based on occurrence number and
   * configuration (smart mode, inits/exits handling).
   *
   * @param int $pad The level to get occurrence path for.
   *
   * @return string Path component (e.g., "inits/", "1/", or "").
   *
   * @global array $padOccur Occurrence counters per level.
   */
  function padInfoTraceOccur ( $pad ) {

    global $padOccur;
    global $padInfoTraceOccurs, $padInfoTraceOccursSmart, $padInfoTraceInitsExits, $padInfoTraceDefault, $padInfoTraceHideDefault;

    if ( $pad < 0 )
      return '';

    $occur = $padOccur [$pad] ?? 0;

    if     ( $padInfoTraceInitsExits and $occur == 0     ) return 'inits/';
    elseif ( $padInfoTraceInitsExits and $occur == 99999 ) return 'exits/';

    if ( ! $padInfoTraceOccurs      ) return '';
    if ( ! $padInfoTraceOccursSmart ) return "$occur/";

    if     ( $occur == 0              ) return '';
    elseif ( $occur == 99999          ) return '';
    elseif ( padInfoTraceDefault ( $pad ) ) return '';
    else                                return "$occur/";

  }



  /**
   * Recursively deletes a trace directory and all contents.
   *
   * Removes all files and subdirectories within the specified
   * directory, then removes the directory itself.
   *
   * @param string $dir The directory path to delete.
   *
   * @return void
   */
  function padInfoTraceDeleteDir( $dir ) {

    if ( ! file_exists ( $dir ) )
      return;

    $files = glob($dir . '*', GLOB_MARK);

    foreach ($files as $file)
        if (is_dir($file))
           padInfoTraceDeleteDir($file);
        else
            unlink($file);

    rmdir($dir);

  }


  /**
   * Checks if a level has default/minimal configuration.
   *
   * Returns TRUE if the level has no special walk mode, single
   * data item, and no before/after/start/end bases defined.
   *
   * @param int $pad The level to check.
   *
   * @return bool TRUE if level has default configuration.
   *
   * @global array $padWalk Walk mode per level.
   * @global array $padData Data arrays per level.
   */
  function padInfoTraceDefault ( $pad ) {

    global $padWalk, $padData, $padBeforeBase, $padAfterBase, $padStartBase, $padEndBase;

    if ( $pad == 0 )
      return TRUE;

    if ( ! isset ( $padWalk [$pad] ) or ! isset ( $padData [$pad] ) )
      return FALSE;

    if ( ! isset ( $padStartBase [$pad] ) or ! isset ( $padEndBase [$pad] ) )
      return FALSE;

    if ( ! isset ( $padBeforeBase [$pad] ) or ! isset ( $padAfterBase [$pad] ) )
      return FALSE;

    if ( $padWalk [$pad] == 'next'
      or count ( $padData [$pad] ) > 1
      or $padBeforeBase [$pad]
      or $padAfterBase  [$pad]
      or $padStartBase  [$pad]
      or $padEndBase    [$pad] )

      return FALSE;

    return TRUE;

  }


  /**
   * Initializes trace tracking for a new level.
   *
   * Sets up level path, child counters, and trace ID for the
   * specified processing level.
   *
   * @param int $pad The level to initialize.
   *
   * @return void
   *
   * @global array $padTag Tag names per level.
   * @global int   $padInfoTraceId Current trace ID.
   */
  function padInfoTraceSet ( $pad ) {

    global $padOccur, $padTag;
    global $padInfoTraceLevel, $padInfoTraceType, $padInfoTraceAddLine;
    global $padInfoTraceIds, $padInfoTraceId, $padInfoTraceLevelChilds, $padInfoTraceOccurChilds;

    if ( $pad < 0 ) {
      $padInfoTraceLevel [$pad] = '';
      return;
    }

    $last = $pad - 1;
    $tag  = $padTag [$pad] ?? "NoTag";
    $add  = padInfoTraceOccur ( $pad-1 );
    $line = ($padInfoTraceAddLine) ? "$padInfoTraceId-" : '';

    $padInfoTraceLevel [$pad] = $padInfoTraceLevel [$last] ?? '';

    $padInfoTraceLevel [$pad] .= "/$add$line$tag";

    $padInfoTraceLevelChilds [$pad] = 0;
    $padInfoTraceOccurChilds [$pad] = [];

    $padInfoTraceIds [$pad] = $padInfoTraceId;

  }


  /**
   * Writes status file for the current level.
   *
   * Creates a status marker file indicating the execution outcome
   * (null, hit, else, other) and data count for the level.
   *
   * @return void
   *
   * @global int   $pad Current processing level.
   * @global array $padNull Null flags per level.
   * @global array $padHit Hit flags per level.
   * @global array $padElse Else flags per level.
   */
  function padInfoTraceStatus ( ) {

    global $pad, $padNull, $padHit, $padElse, $padData, $padInfoTraceLevel;

    if     ( $padNull [$pad] ) $status = padInfoTraceStatusGo ( 'null'  );
    elseif ( $padHit  [$pad] ) $status = padInfoTraceStatusGo ( 'hit'   );
    elseif ( $padElse [$pad] ) $status = padInfoTraceStatusGo ( 'else'  );
    else                       $status = padInfoTraceStatusGo ( 'other' );

    if ( ! padIsDefaultData ( $padData [$pad] ) and count ( $padData [$pad] ) )
      $status .= '-' . count ( $padData [$pad] ) ;

    padInfoTraceWrite ( $pad, "status-$status.txt", $status, 'file' );

  }


  /**
   * Builds status string based on result and base state.
   *
   * Appends suffix to type based on whether result was true,
   * else, no-base, or just result.
   *
   * @param string $type The base status type (null, hit, else, other).
   *
   * @return string Status string with appropriate suffix.
   *
   * @global int   $pad Current processing level.
   * @global array $padResult Result flags per level.
   * @global array $padBase Base values per level.
   */
  function padInfoTraceStatusGo ( $type ) {

    global $pad, $padResult, $padBase, $padBase, $padFalse;

    if ( $padResult [$pad] and $padBase [$pad] )
      if     ( $padBase [$pad] == $padBase  [$pad] )     return $type . '-true';
      elseif ( $padBase [$pad] == $padFalse )            return $type . '-else';

    if     ( ! $padResult [$pad] and ! $padBase [$pad] ) return $type . '-no-base';
    elseif ( $padResult [$pad]                         ) return $type . '-result';
    else                                                 return $type;

  }


  /**
   * Renames trace directory to include child count.
   *
   * Appends the child count to the directory name for easier
   * identification in trace output.
   *
   * @param string $dir    The directory path relative to trace dir.
   * @param int    &$childs The child count to append.
   * @param string $type   The type (unused).
   *
   * @return void
   */
  function padInfoTraceChilds ( $dir, &$childs, $type ) {

    global $pad, $padOccur, $padInfoTraceLevel, $padInfoTraceDir;

    if ( ! $dir or ! $childs )
      return;

    $dir = "$padInfoTraceDir/$dir";

    $new  = $dir . '-' . $childs;
    $from = DAT . $dir;
    $to   = DAT . $new;

    if ( ! file_exists ( $from ) or file_exists ( $to) )
      return;

    rename ( $from, $to );

  }


  /**
   * Removes duplicate trace files in a directory.
   *
   * Compares pairs of trace files (trace/local, trace/tree, etc.)
   * and removes duplicates based on file size.
   *
   * @param string $dir The directory to check.
   *
   * @return void
   */
  function padInfoTraceCheckLocal ( $dir ) {

    padInfoTraceCheckLocalOne ( $dir, 'trace', 'local' );
    padInfoTraceCheckLocalOne ( $dir, 'trace', 'tree' );
    padInfoTraceCheckLocalOne ( $dir, 'local', 'tree' );

    padInfoTraceCheckLocalOne ( $dir, 'root', 'local' );
    padInfoTraceCheckLocalOne ( $dir, 'root', 'tree' );
    padInfoTraceCheckLocalOne ( $dir, 'root', 'trace' );

  }


  /**
   * Removes second file if it matches first by size.
   *
   * Compares two trace files and deletes the second if they
   * have identical sizes (assumed to be duplicates).
   *
   * @param string $dir   The directory containing the files.
   * @param string $file1 First filename (without extension).
   * @param string $file2 Second filename to potentially remove.
   *
   * @return void
   */
  function padInfoTraceCheckLocalOne ( $dir, $file1, $file2 ) {

    global $padInfoTraceDir;

    $file1 = DAT . "$padInfoTraceDir/$dir/$file1.txt";
    $file2 = DAT . "$padInfoTraceDir/$dir/$file2.txt";

    $file1 = str_replace ( '//', '/', $file1 );
    $file2 = str_replace ( '//', '/', $file2 );

    if ( ! file_exists ( $file1 ) or ! file_exists ( $file2 ) )
      return;

    if ( filesize ( $file1 ) == filesize ( $file2 ) )
      unlink ( $file2 );

  }


?>
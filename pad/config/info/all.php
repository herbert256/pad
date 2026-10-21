<?php

  // $padInfo selector 'all': switches on every debug facility at once.
  //
  // Reached as config/info/all.php from inits/config.php (or inits/configSet.php) for each
  // comma-separated word in $padInfo. Enables all five info types - track, xml, stats, trace,
  // xref - and then every sub-flag each of them honours, so nothing the engine can record is
  // left out. Expensive; none.php is its mirror image.
  //
  // Every flag is explained here, once - the other files of config/info/ set subsets of them.

  // The five info types. track files what the request received and answered, in DATA/track/
  // and the PAD database as the Track flags below choose. xml writes the tree of levels and
  // occurrences the page ran to DATA/_xml/. stats times the request - total, boot, usr (the
  // engine), call (application PHP) - and sends that as the PAD-Stats header of a web answer
  // (and as 'stats' lines of the trace). trace writes the execution trace under
  // DATA/trace/<page>/<request id>-<n>/. xref appends "<app>;<page>" to
  // DATA/reference/<kind>/<name>.txt for each tag, function, option, sequence ... the page's
  // own template uses - what the reference application reads.

  $padInfoTrack            = TRUE;
  $padInfoXml              = TRUE;
  $padInfoStats            = TRUE;
  $padInfoTrace            = TRUE;
  $padInfoXref             = TRUE;

  // Where track files the request. FileRequest: DATA/track/requests/<request id>.json - what
  // came in (headers, GET, POST, files, cookies, body, $_SERVER, secrets redacted) is written
  // as <request id>-entry.json at the start, and at the end the whole goes in one file with
  // the request's ids and times and the answer: status, PHP and PAD headers, length, body.
  // FileData: the page as sent, DATA/track/data/complete/<page>.html (include/ for an
  // included page), each request over the last. DbSession: a row per session in
  // track_session of the PAD database, counting its requests. DbRequest: a row per request
  // in track_request - page, duration, length, status, etag, URI, referer, address, user
  // agent - and the session's row with it. DbData: the page in track_data, once per etag.
  // The three tables are made by pad/database/pad.sql.

  $padInfoTrackFileRequest = TRUE;
  $padInfoTrackFileData    = TRUE;
  $padInfoTrackDbSession   = TRUE;
  $padInfoTrackDbRequest   = TRUE;
  $padInfoTrackDbData      = TRUE;

  // The xml report, DATA/_xml/complete/<page>.xml (include/ for an included page). Parms: a
  // level with more than one option, parameter or $/% variable gets a <parms> block of them
  // in place of the parm= attribute with its first option. Tidy: the file is re-indented by
  // PHP's tidy extension once written - nothing without the extension. Compact: the bare
  // skeleton instead - no attributes, no occurrences, no <parms>, a level that produced no
  // output left out - written to DATA/_xml/compact/...; on here, so the compact file is the
  // only one this selector writes and Parms shows nothing in it.

  $padInfoXmlParms         = TRUE;
  $padInfoXmlTidy          = TRUE;
  $padInfoXmlCompact       = TRUE;

  // How the trace writes. Lines: the trace lines are written at all - off, only the files
  // of the trace (data*.json, status-*.txt, more/) are. Double: a level's and an
  // occurrence's result lines are written even when they equal the level's content, and the
  // content lines (True, False, LevelBase below) are not written at all - on here, so those
  // three show nothing. Default: a level on PAD's default data (one empty row - a tag that
  // does not iterate) gets its data traced too, and a pass on an empty row its row.
  // KeepEmpty: a level directory in which no occurrence ran is kept; off, it is deleted when
  // the level ends.

  $padInfoTraceLines       = TRUE;
  $padInfoTraceDouble      = TRUE;
  $padInfoTraceDefault     = TRUE;
  $padInfoTraceKeepEmpty   = TRUE;

  // Where the lines go, under DATA/trace/<page>/<request id>-<n>/. Root: every line to
  // root.txt at the top. Tree: to tree.txt in the directory of the current level and of
  // every level around it, up to where the trace began. Local: to local.txt of the current
  // level or occurrence only. Types: also to a file per kind of line - level, occur, field,
  // call, file ... - at the current level and at the top. More: a line's text longer than 70
  // characters is cut to 63 and ends in <more>; this keeps the whole in more/<line>.txt.
  // Dump: when the trace ends, a full dump of the request as an error dump has it - written
  // to DATA/dumps/<app>/trace/<page>/<request id>-<n>/dump/, not into the trace itself.

  $padInfoTraceRoot        = TRUE;
  $padInfoTraceDump        = TRUE;
  $padInfoTraceTree        = TRUE;
  $padInfoTraceLocal       = TRUE;
  $padInfoTraceTypes       = TRUE;
  $padInfoTraceMore        = TRUE;

  // The start and end lines. StartEnd: 'trace start' and 'trace end' where the trace begins
  // and ends - the request, or a {trace} tag - the end with the result. StartEndLvl: 'level
  // start' with the tag as written and 'level end' with what the level produced.
  // StartEndOcc: 'occur start' and 'occur end' with the output of that pass.

  $padInfoTraceStartEnd    = TRUE;
  $padInfoTraceStartEndLvl = TRUE;
  $padInfoTraceStartEndOcc = TRUE;

  // The trace's directories. Status: when a level ends, a file in its directory named for
  // how it went - status-hit-true-3.txt: null, hit, else or other, the branch it rendered
  // and, when it iterated, its number of rows. Childs: a level's and an occurrence's
  // directory get the number of occurrences that ran in them appended, sequence-3. AddLine:
  // a level's directory starts with the number of the trace line it was made at,
  // 133-sequence. LocalChk: a tree.txt the same size as the local.txt beside it, or either
  // the same size as a root.txt there, is deleted as a copy - when a level or an occurrence
  // ends, and at the top when the trace ends.

  $padInfoTraceStatus      = TRUE;
  $padInfoTraceChilds      = TRUE;
  $padInfoTraceAddLine     = TRUE;
  $padInfoTraceLocalChk    = TRUE;

  // Occurrence directories. Occurs: each pass of a level gets a directory of its own inside
  // the level's, 1/, 2/ ... - off, a pass writes into the level's directory. OccursSmart:
  // none for a level that passes once (one row, no @start@/@end@ or before/after part - the
  // function padInfoTraceDefault decides, not the flag of that name), nor for what runs
  // before the first pass and after the last; left off here, so every pass gets one.

  $padInfoTraceOccurs      = TRUE;
  $padInfoTraceOccursSmart = FALSE;

  // InitsExits: what a level does before its first pass and after its last (its occurrences
  // 0 and 99999) goes to inits/ and exits/ in its directory, not to 0/ and 99999/ - or,
  // with Occurs off, to the level's directory itself.

  $padInfoTraceInitsExits  = TRUE;

  // TypesDir: the per-kind files of Types go in a types/ directory, types/level.txt, in
  // place of types-level.txt beside the other files.

  $padInfoTraceTypesDir    = TRUE;

  // Build: the page's template as assembled - the page inside its _inits.pad and _exits.pad
  // wrappers - as one 'build info' line, before the first level runs.

  $padInfoTraceBuild       = TRUE;

  // Parse: read nowhere at present - events/parse.php, which would trace each tag as it is
  // found with the text around it, is included by no file. Parms: a 'parm' line per tag for
  // each option (opt), named parameter (prm), $ variable (lvl) and % variable (occ) on it.

  $padInfoTraceParse       = TRUE;
  $padInfoTraceParms       = TRUE;

  // The text of a level. Content, with Double off: the content lines are written, and a
  // result line equal to the level's text is left out. True: 'level true', the text the
  // level runs - the same text LevelBase writes, the @else@ part when the tag missed. False:
  // 'level false', the @else@ part. LevelBase: 'level base', the text the level runs.
  // ResultLvl: 'level result', what the tag's handler answered. ResultOcc: 'occ-result',
  // the output of each pass. With Double on, as here, True, False and LevelBase write
  // nothing.

  $padInfoTraceContent     = TRUE;
  $padInfoTraceTrue        = TRUE;
  $padInfoTraceFalse       = TRUE;
  $padInfoTraceLevelBase   = TRUE;
  $padInfoTraceResultLvl   = TRUE;
  $padInfoTraceResultOcc   = TRUE;

  // Flags: a 'level flags' line - hit, else, null, array and the number of rows - when the
  // tag's result is classified, and again with the level's content lines.

  $padInfoTraceFlags       = TRUE;

  // DataLvl: a level's data, the rows it iterates, as a 'level data' line and as data.json in
  // its directory. DataOcc: each pass's row as an 'occ-data' line and as data-<n>.json. A
  // level on PAD's default data, and an empty row, only with Default on.

  $padInfoTraceDataLvl     = TRUE;
  $padInfoTraceDataOcc     = TRUE;

  // Lines for what the page does along the way:
  //
  //   Store     a {data}, {content} or {bool} block stored - its name and value
  //   Sequence  a sequence run - its type and the terms it gave
  //   Var       read nowhere at present: events/var_start.php and var_end.php, which would
  //             trace a {$var} and its value, are included by no file
  //   Field     every field lookup through padField - name, lookup type, level and the
  //             value found; the busiest of all
  //   Eval      read nowhere at present: events/eval/*, which would trace an expression
  //             step by step, are included by no file
  //   Call      every PHP file the engine runs - the page's .php, _inits, _lib, a tag ...
  //   Sql       every statement of db() and padDb() - input, values, the SQL sent, result
  //   Put       every file padFilePut writes, the trace's own files left out
  //   Get       every file padFileGet reads
  //   Curl      every padCurl fetch, by its URL

  $padInfoTraceStore       = TRUE;
  $padInfoTraceSequence    = TRUE;
  $padInfoTraceVar         = TRUE;
  $padInfoTraceField       = TRUE;
  $padInfoTraceEval        = TRUE;
  $padInfoTraceCall        = TRUE;
  $padInfoTraceSql         = TRUE;
  $padInfoTracePut         = TRUE;
  $padInfoTraceGet         = TRUE;
  $padInfoTraceCurl        = TRUE;

?>

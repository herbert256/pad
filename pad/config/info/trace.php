<?php

  // $padInfo selector 'trace': enables the execution trace (info/types/trace/) with a
  // readable default set of detail flags rather than everything.
  //
  // On are the root level, line numbers, local tags, the start/end markers for levels and
  // occurrences, and the content each level produced; off are the noisier per-tag internals
  // - the tree, types, parse/parms/options, flags, data and the store/eval/sql/curl event
  // lines. Also included directly by tags/trace.php, so a {trace} tag traces just its own
  // part of the page with exactly these settings.

  $padInfoTrace            = TRUE;

  $padInfoTraceLines       = TRUE;
  $padInfoTraceDouble      = FALSE;
  $padInfoTraceDefault     = FALSE;
  $padInfoTraceKeepEmpty   = FALSE;

  $padInfoTraceRoot        = TRUE;
  $padInfoTraceTree        = FALSE;
  $padInfoTraceLocal       = TRUE;
  $padInfoTraceTypes       = FALSE;
  $padInfoTraceMore        = FALSE;

  $padInfoTraceXref        = FALSE;
  $padInfoTraceDump        = FALSE;

  $padInfoTraceStartEnd    = TRUE;
  $padInfoTraceStartEndLvl = TRUE;
  $padInfoTraceStartEndOcc = TRUE;

  $padInfoTraceStatus      = FALSE;
  $padInfoTraceChilds      = FALSE;
  $padInfoTraceAddLine     = FALSE;
  $padInfoTraceLocalChk    = FALSE;

  $padInfoTraceOccurs      = FALSE;
  $padInfoTraceOccursSmart = FALSE;

  $padInfoTraceInitsExits  = FALSE;

  $padInfoTraceTypesDir    = FALSE;
  $padInfoTraceRequest     = FALSE;

  $padInfoTraceSession     = FALSE;
  $padInfoTraceBuild       = FALSE;

  $padInfoTraceParse       = FALSE;
  $padInfoTraceParms       = FALSE;
  $padInfoTraceOptions     = FALSE;

  $padInfoTraceStartLvl    = FALSE;
  $padInfoTraceStartOcc    = FALSE;
  $padInfoTraceEndLvl      = FALSE;
  $padInfoTraceEndOcc      = FALSE;

  $padInfoTraceContent     = TRUE;
  $padInfoTraceTrue        = FALSE;
  $padInfoTraceFalse       = FALSE;
  $padInfoTraceLevelBase   = FALSE;
  $padInfoTraceResultLvl   = FALSE;
  $padInfoTraceResultOcc   = FALSE;

  $padInfoTraceFlags       = FALSE;

  $padInfoTraceDataLvl     = FALSE;
  $padInfoTraceDataOcc     = FALSE;

  $padInfoTraceStore       = FALSE;
  $padInfoTraceSequence    = FALSE;
  $padInfoTraceVar         = FALSE;
  $padInfoTraceField       = FALSE;
  $padInfoTraceEval        = FALSE;
  $padInfoTraceCall        = FALSE;
  $padInfoTraceSql         = FALSE;
  $padInfoTracePut         = FALSE;
  $padInfoTraceGet         = FALSE;
  $padInfoTraceCurl        = FALSE;

?>
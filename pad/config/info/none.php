<?php

  // $padInfo selector 'none': the mirror image of all.php, switching every debug facility and
  // every sub-flag off.
  //
  // Useful as the first word of a $padInfo list to wipe the slate before naming the few
  // things wanted, e.g. padInfo=none,trace.

  $padInfoTrack            = FALSE;
  $padInfoXml              = FALSE;
  $padInfoStats            = FALSE;
  $padInfoTrace            = FALSE;
  $padInfoXref             = FALSE;

  $padInfoTrackFileRequest = FALSE;
  $padInfoTrackFileData    = FALSE;
  $padInfoTrackDbSession   = FALSE;
  $padInfoTrackDbRequest   = FALSE;
  $padInfoTrackDbData      = FALSE;

  $padInfoXmlParms         = FALSE;
  $padInfoXmlTidy          = FALSE;
  $padInfoXmlCompact       = FALSE;

  $padInfoTraceLines       = FALSE;
  $padInfoTraceDouble      = FALSE;
  $padInfoTraceDefault     = FALSE;
  $padInfoTraceKeepEmpty   = FALSE;

  $padInfoTraceRoot        = FALSE;
  $padInfoTraceDump        = FALSE;
  $padInfoTraceTree        = FALSE;
  $padInfoTraceLocal       = FALSE;
  $padInfoTraceTypes       = FALSE;
  $padInfoTraceMore        = FALSE;

  $padInfoTraceStartEnd    = FALSE;
  $padInfoTraceStartEndLvl = FALSE;
  $padInfoTraceStartEndOcc = FALSE;

  $padInfoTraceStatus      = FALSE;
  $padInfoTraceChilds      = FALSE;
  $padInfoTraceAddLine     = FALSE;
  $padInfoTraceLocalChk    = FALSE;

  $padInfoTraceOccurs      = FALSE;
  $padInfoTraceOccursSmart = FALSE;

  $padInfoTraceInitsExits  = FALSE;

  $padInfoTraceTypesDir    = FALSE;

  $padInfoTraceBuild       = FALSE;

  $padInfoTraceParse       = FALSE;
  $padInfoTraceParms       = FALSE;

  $padInfoTraceContent     = FALSE;
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
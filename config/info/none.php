<?php
  
  $padInfTrack            = FALSE;       // Big Brother, session and request information of the client
  $padInfXml              = FALSE;       // Build a XML file of the structure of the PAD page 
  $padInfXref             = FALSE;       // Build the <data>xref directories
  $padInfStats            = FALSE;       // Keep runtime statistics about time and cpu used
  $padInfTrace            = FALSE;       // Trace the internal working of PAD
  $padInfXapp             = FALSE;       // Build the <app>_xapp directorie

  $padInfTrackFileRequest = FALSE;       // Request info in the data directory
  $padInfTrackFileData    = FALSE;       // Complete result page in the directory
  $padInfTrackDbSession   = FALSE;       // Session info in the PAD database
  $padInfTrackDbRequest   = FALSE;       // Request info in the PAD database
  $padInfTrackDbData      = FALSE;       // Complete result page in the PAD database

  $padInfXmlParms         = FALSE;
  $padInfXmlTidy          = FALSE;
  $padInfXmlShowEmpty     = FALSE;
  
  $padInfXrefXref         = FALSE;
  $padInfXrefPage         = FALSE;
  $padInfXrefTree         = FALSE;
  $padInfXrefXml          = FALSE;
  $padInfXrefTrace        = FALSE;

  $padInfTraceLines       = FALSE;
  $padInfTraceDouble      = FALSE;
  $padInfTraceDefault     = FALSE; 
  $padInfTraceKeepEmpty   = FALSE;

  $padInfTraceRoot        = FALSE;
  $padInfTraceTree        = FALSE;  
  $padInfTraceLocal       = FALSE;
  $padInfTraceTypes       = FALSE;
  $padInfTraceMore        = FALSE;
  $padInfTraceXref        = FALSE;

  $padInfTraceStartEnd    = FALSE;
  $padInfTraceStartEndLvl = FALSE;
  $padInfTraceStartEndOcc = FALSE;

  $padInfTraceStatus      = FALSE;
  $padInfTraceChilds      = FALSE;
  $padInfTraceAddLine     = FALSE;
  $padInfTraceLocalChk    = FALSE;       

  $padInfTraceOccurs      = FALSE;
  $padInfTraceOccursSmart = FALSE;

  $padInfTraceInitsExits  = FALSE;

  $padInfTraceTypesDir    = FALSE;       
  $padInfTraceRequest     = FALSE;

  $padInfTraceSession     = FALSE;
  $padInfTraceBuild       = FALSE;
  
  $padInfTraceParse       = FALSE;
  $padInfTraceParms       = FALSE;
  $padInfTraceOptions     = FALSE;

  $padInfTraceStartLvl    = FALSE;
  $padInfTraceStartOcc    = FALSE;
  $padInfTraceEndLvl      = FALSE;
  $padInfTraceEndOcc      = FALSE;
  
  $padInfTraceContent     = FALSE;
  $padInfTraceTrue        = FALSE;
  $padInfTraceFalse       = FALSE;
  $padInfTraceLevelBase   = FALSE;
  $padInfTraceResultLvl   = FALSE;
  $padInfTraceResultOcc   = FALSE;

  $padInfTraceFlags       = FALSE;
 
  $padInfTraceDataLvl     = FALSE;
  $padInfTraceDataOcc     = FALSE;
  
  $padInfTraceStore       = FALSE;
  $padInfTraceSequence    = FALSE;
  $padInfTraceVar         = FALSE;
  $padInfTraceField       = FALSE;
  $padInfTraceEval        = FALSE;
  $padInfTraceCall        = FALSE;
  $padInfTraceSql         = FALSE;
  $padInfTraceGet         = FALSE;
  $padInfTracePut         = FALSE;
  $padInfTraceCurl        = FALSE;

?>
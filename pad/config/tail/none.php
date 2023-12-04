<?php
  
  $padTailMeta         = FALSE;        // Keep a meta tail log for trace/xml/xref events.
  $padTailRequest      = FALSE;   
  $padTailSession      = FALSE;   
  $padTailOutput       = FALSE;   
  $padTailDump         = FALSE;   

  $padRequest          = TRUE;       // Log the details of the HTTP(s) request 

  $padTrack            = FALSE;       // Big Brother, session and request information of the client
 
  $padTrackFileRequest = FALSE;        // Request info in the data directory
  $padTrackFileData    = FALSE;        // Complete result page in the directory

  $padTrackDbSession   = FALSE;       // Session info in the PAD database
  $padTrackDbRequest   = FALSE;       // Request info in the PAD database
  $padTrackDbData      = FALSE;       // Complete result page in the PAD database

  $padXml              = FALSE;       // Build a XML file of the structure of the PAD page
  $padXmlDetails       = FALSE;
  $padXmlTidy          = FALSE;
  
  $padXref             = FALSE;       // Build the <app>_xref and <data>xref directories
  $padXrefTail         = FALSE;
  $padXrefManual       = FALSE;
  $padXrefDevelop      = FALSE;
  $padXrefXml          = FALSE;
  $padXrefTrace        = FALSE;

  $padStats            = FALSE;

  $padTrace            = FALSE;       // Trace the internal working of PAD

  $padTraceLines       = FALSE;
  $padTraceDouble      = FALSE;
  $padTraceDefault     = FALSE;
  $padTraceKeepEmpty   = FALSE;

  $padTraceRoot        = FALSE;
  $padTraceTree        = FALSE;  
  $padTraceLocal       = FALSE;
  $padTraceTypes       = FALSE;
  $padTraceMore        = FALSE;
  $padTraceXref        = FALSE;

  $padTraceStartEnd    = FALSE;
  $padTraceStartEndLvl = FALSE;
  $padTraceStartEndOcc = FALSE;

  $padTraceStatus      = FALSE;
  $padTraceChilds      = FALSE;
  $padTraceAddLine     = FALSE;
  $padTraceLocalChk    = FALSE;       

  $padTraceOccurs      = FALSE;
  $padTraceOccursSmart = FALSE;

  $padTraceInitsExits  = FALSE;

  $padTraceTypesDir    = FALSE;       
  $padTraceRequest     = FALSE;

  $padTraceSession     = FALSE;
  $padTraceBuild       = FALSE;
  
  $padTraceParse       = FALSE;
  $padTraceParms       = FALSE;
  $padTraceOptions     = FALSE;

  $padTraceStartLvl    = FALSE;
  $padTraceStartOcc    = FALSE;
  $padTraceEndLvl      = FALSE;
  $padTraceEndOcc      = FALSE;
  
  $padTraceContent     = FALSE;
  $padTraceTrue        = FALSE;
  $padTraceFalse       = FALSE;
  $padTraceLevelBase   = FALSE;
  $padTraceResultLvl   = FALSE;
  $padTraceResultOcc   = FALSE;

  $padTraceFlags       = FALSE;
 
  $padTraceDataLvl     = FALSE;
  $padTraceDataOcc     = FALSE;
  
  $padTraceStore       = FALSE;
  $padTraceSequence    = FALSE;
  $padTraceVar         = FALSE;
  $padTraceField       = FALSE;
  $padTraceEval        = FALSE;
  $padTraceCall        = FALSE;
  $padTraceSql         = FALSE;
  $padTraceGet         = FALSE;
  $padTraceCurl        = FALSE;

?>
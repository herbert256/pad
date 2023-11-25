<?php
  
  $padTailMeta         = TRUE;        // Keep a meta tail log for trace/xml/xref events.
  $padTailRequest      = TRUE;   
  $padTailSession      = TRUE;   
  $padTailOutput       = TRUE;   
  $padTailDump         = TRUE;   

  $padRequest          = FALSE;       // Log the details of the HTTP(s) request 

  $padTrack            = FALSE;       // Big Brother, session and request information of the client
 
  $padTrackFileRequest = TRUE;        // Request info in the data directory
  $padTrackFileData    = TRUE;        // Complete result page in the directory

  $padTrackDbSession   = FALSE;       // Session info in the PAD database
  $padTrackDbRequest   = FALSE;       // Request info in the PAD database
  $padTrackDbData      = FALSE;       // Complete result page in the PAD database

  $padXml              = FALSE;       // Build a XML file of the structure of the PAD page
  $padXmlDetails       = FALSE;
  $padXmlTidy          = FALSE;
  
  $padXref             = FALSE;       // Build the <app>_xref and <data>xref directories
  $padXrefTail         = TRUE;
  $padXrefManual       = FALSE;
  $padXrefDevelop      = FALSE;
  $padXrefXml          = FALSE;
  $padXrefTrace        = FALSE;

  $padTrace            = FALSE;       // Trace the internal working of PAD
    
  $padTraceLines       = TRUE;
  $padTraceDouble      = FALSE;
  $padTraceDefault     = FALSE;
  $padTraceKeepEmpty   = FALSE;

  $padTraceRoot        = TRUE;
  $padTraceTree        = TRUE;  
  $padTraceLocal       = TRUE;
  $padTraceTypes       = FALSE;
  $padTraceMore        = FALSE;
  $padTraceXref        = FALSE;

  $padTraceStartEnd    = TRUE;
  $padTraceStartEndLvl = TRUE;
  $padTraceStartEndOcc = TRUE;

  $padTraceStatus      = FALSE;
  $padTraceChilds      = FALSE;
  $padTraceAddLine     = TRUE;
  $padTraceLocalChk    = TRUE;       

  $padTraceOccurs      = TRUE;
  $padTraceOccursSmart = TRUE;

  $padTraceInitsExits  = FALSE;

  $padTraceTypesDir    = TRUE;       
  $padTraceRequest     = TRUE;

  $padTraceSession     = TRUE;
  $padTraceBuild       = TRUE;
  
  $padTraceParse       = TRUE;
  $padTraceParms       = TRUE;
  $padTraceOptions     = TRUE;

  $padTraceStartLvl    = TRUE;
  $padTraceStartOcc    = TRUE;
  $padTraceEndLvl      = TRUE;
  $padTraceEndOcc      = TRUE;
  
  $padTraceContent     = TRUE;
  $padTraceTrue        = TRUE;
  $padTraceFalse       = TRUE;
  $padTraceLevelBase   = TRUE;
  $padTraceResultLvl   = TRUE;
  $padTraceResultOcc   = TRUE;

  $padTraceFlags       = TRUE;
 
  $padTraceDataLvl     = TRUE;
  $padTraceDataOcc     = TRUE;
  
  $padTraceStore       = TRUE;
  $padTraceSequence    = TRUE;
  $padTraceVar         = TRUE;
  $padTraceField       = TRUE;
  $padTraceEval        = TRUE;
  $padTraceCall        = TRUE;
  $padTraceExists      = TRUE;
  $padTraceSql         = TRUE;
  $padTraceGet         = TRUE;
  $padTraceCurl        = TRUE;

?>
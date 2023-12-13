<?php
  
  $padMain             = TRUE;       // Main information about a PAD request
  $padRequest          = TRUE;       // Log the details of the HTTP(s) request 
  $padTrack            = TRUE;       // Big Brother, session and request information of the client
  $padXml              = TRUE;       // Build a XML file of the structure of the PAD page 
  $padXref             = TRUE;       // Build the <app>_xref and <data>xref directories
  $padStats            = TRUE;       // Keep runtime statistics about time and cpu used
  $padTrace            = TRUE;       // Trace the internal working of PAD

  $padMainMeta         = TRUE;       // Keep a meta tail log for trace/xml/xref events. 
  $padMainRequest      = TRUE;   
  $padMainSession      = TRUE;   
  $padMainOutput       = TRUE;   
  $padMainDump         = TRUE;   

  $padTrackFileRequest = TRUE;       // Request info in the data directory
  $padTrackFileData    = TRUE;       // Complete result page in the directory
  $padTrackDbSession   = TRUE;       // Session info in the PAD database
  $padTrackDbRequest   = TRUE;       // Request info in the PAD database
  $padTrackDbData      = TRUE;       // Complete result page in the PAD database

  $padXmlParms         = TRUE;
  $padXmlTidy          = TRUE;
  $padXmlShowEmpty     = TRUE;
  
  $padXrefTail         = TRUE;
  $padXrefManual       = TRUE;
  $padXrefDevelop      = TRUE;
  $padXrefXml          = TRUE;
  $padXrefTrace        = TRUE;
  
  $padTraceLines       = TRUE;
  $padTraceDouble      = TRUE;
  $padTraceDefault     = TRUE;
  $padTraceKeepEmpty   = TRUE;

  $padTraceRoot        = TRUE;
  $padTraceTree        = TRUE;  
  $padTraceLocal       = TRUE;
  $padTraceTypes       = TRUE;
  $padTraceMore        = TRUE;
  $padTraceXref        = TRUE;

  $padTraceStartEnd    = TRUE;
  $padTraceStartEndLvl = TRUE;
  $padTraceStartEndOcc = TRUE;

  $padTraceStatus      = TRUE;
  $padTraceChilds      = TRUE;
  $padTraceAddLine     = TRUE;
  $padTraceLocalChk    = TRUE;       

  $padTraceOccurs      = TRUE;
  $padTraceOccursSmart = FALSE;

  $padTraceInitsExits  = TRUE;
  
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
  $padTraceSql         = TRUE;
  $padTraceGet         = TRUE;
  $padTraceCurl        = TRUE;

?>
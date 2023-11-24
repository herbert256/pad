<?php
  
  $padTailMeta         = TRUE;        // Keep a meta tail log for trace/xml/xref events.
  $padTailMetaNoTrace  = FALSE;       // Trace lines are only used for the tail meta log.
  $padTailMetaNoXml    = FALSE;       // Xml lines are only used for the tail meta log.
  $padTailMetaNoXref   = FALSE;       // Xref lines are only used for the tail meta log.
 
  $padTailRequest      = TRUE;   
  $padTailSession      = TRUE;   
  $padTailOutput       = TRUE;   
  $padTailDump         = TRUE;   
 
  $padTrackFileRequest = TRUE;        // Request info in the data directory
  $padTrackFileData    = TRUE;        // Complete result page in the directory

  $padTrackDbSession   = FALSE;       // Session info in the PAD database
  $padTrackDbRequest   = FALSE;       // Request info in the PAD database
  $padTrackDbData      = FALSE;       // Complete result page in the PAD database

  $padTraceProfile     = 'default';   // 'default' / 'all' / 'none'
                                      // There must be a file with the same name in folder trace

  $padXmlDetails       = FALSE;
  $padXmlTidy          = FALSE;
  
  $padXrefTail         = TRUE;
  $padXrefManual       = FALSE;
  $padXrefDevelop      = FALSE;
  $padXrefXml          = FALSE;
  $padXrefTrace        = FALSE;
  
?>
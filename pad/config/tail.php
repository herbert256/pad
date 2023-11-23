<?php
  
  $padTail             = TRUE;    // Keep a meta tail log for trace/xml/xref events
  $padTailNoTrace      = TRUE;    // Trace lines are only used for the tail log itself.
  $padTailNoXml        = TRUE;    // Xml lines are only used for the tail log itself.
  $padTailNoXref       = TRUE;    // Xref lines are only used for the tail log itself.
  
  $padTrackFileRequest = TRUE;   // Request info in the data directory
  $padTrackFileData    = TRUE;   // Complete result page in the directory

  $padTrackDbSession   = TRUE;   // Session info in the PAD database
  $padTrackDbRequest   = TRUE;   // Request info in the PAD database
  $padTrackDbData      = TRUE;   // Complete result page in the PAD database

  $padTraceProfile     = 'all';   // 'default' / 'all' / 'none'
                                  // There must be a file with the same name in folder trace

  $padXmlDetails       = FALSE;
  $padXmlTidy          = TRUE;
  
  $padXrefManual       = TRUE;
  $padXrefDevelop      = TRUE;
  $padXrefReverse      = TRUE;
  $padXrefXml          = TRUE;
  $padXrefTrace        = TRUE;
  
?>
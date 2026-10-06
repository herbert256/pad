<?php

  // Starts the 'xml' info mode, which renders the processing tree of a page as XML.
  //
  // Nothing is written while the page runs: the level/ and occur/ files build $padInfoXmlTree,
  // the node per level, with $padInfoXmlLevel mapping the engine level $pad onto a node, and
  // append to $padInfoXmlEvents, the ordered log info/types/xml/end.php later replays.
  //
  // Also picks the output file - DATA/_xml/complete/<page>.xml, or include/ for an included
  // page, with compact/ inserted when $padInfoXmlCompact - and deletes an earlier copy.
  // The name is relative to DATA, where padFilePut writes it; the delete looked for it
  // relative to the working directory, never found it, and every request appended one more
  // copy of the tree to the same file.

  global $padInfoXmlCompact;

  include_once PAD . 'info/types/xml/_lib.php';

  $padInfoXmlId     = 0;
  $padInfoXmlDepth  = 0;
  $padInfoXmlTree   = [];
  $padInfoXmlLevel  = [];
  $padInfoXmlEvents = [];

  if ( padInclude () )
    $padInfoXmlFile = "_xml/include/$padStartPage.xml";
  else
    $padInfoXmlFile = "_xml/complete/$padStartPage.xml";

  if ( $padInfoXmlCompact )
    $padInfoXmlFile = str_replace('_xml/', '_xml/compact/', $padInfoXmlFile);

  if ( file_exists ( DATA . $padInfoXmlFile ) )
    unlink ( DATA . $padInfoXmlFile );

?>

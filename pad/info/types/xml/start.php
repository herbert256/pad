<?php

  // Starts the 'xml' info mode, which renders the processing tree of a page as XML.
  //
  // Nothing is written while the page runs: the level/ and occur/ files build $padInfoXmlTree,
  // the node per level, with $padInfoXmlLevel mapping the engine level $pad onto a node, and
  // append to $padInfoXmlEvents, the ordered log info/types/xml/end.php later replays.
  //
  // Also picks the output file - DATA/_xml/complete/<page>.xml, or include/ for an included
  // page, with compact/ inserted when $padInfoXmlCompact. The end writes it whole, over an
  // earlier copy: deleted here first, it was one more race for a request beside it - a file
  // another one had just deleted failed the unlink.

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

?>

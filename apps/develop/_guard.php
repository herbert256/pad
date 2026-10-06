<?php

  // develop's tools act on a plain GET: ?clean rewrites every .php and .pad file of the
  // checkout, ?build wipes the suite results and the dumps and starts thousands of fetches,
  // ?coverage, ?replay and ?benchmark write and delete under DATA/, the pages of sequence/
  // rewrite the engine's flags/ files - and nothing stood in their way, so any visitor
  // could start them, and so could an image on any page the developer opened. They answer
  // this machine only now (developLocal, _lib/develop.php); everyone else gets a 403.
  //
  // The index stays open: it only lists the tools, and the static copy of the site
  // (pages.sh) carries it.

  if ( $padPage == 'index' )
    return TRUE;

  return developLocal ();

?>

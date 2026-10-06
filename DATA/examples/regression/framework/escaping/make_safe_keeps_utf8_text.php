<?php

  // padMakeSafe keeps text as the UTF-8 it is - it blanked every byte above 0x7F, so the
  // examples search turned "Zoë Müller" into "Zo M ller" - while a control character still
  // becomes a space, and a cut does not split a character.

  $safeName = padMakeSafe ( "Zoë Müller" );
  $safeLine = padMakeSafe ( "one\ntwo" );
  $safeCut  = padMakeSafe ( "ééé", 3 );

?>

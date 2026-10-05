<?php

  // {parms title, subtitle='', tone='info'} at the top of a custom tag's template declares
  // the parameters the tag takes: a name alone is required, name=default fills in what the
  // caller left out, readable as {#tone} like a given one. A tag that declares them has a
  // parameter it does not declare reported under the strict check. padParmsDeclare in
  // lib/slot.php; the items are read raw, as {attrs} reads them.

  $padParmsOwner = padSlotOwner ();

  if ( $padParmsOwner === FALSE ) {
    if ( $padCheckSyntax )
      padError ( "the {parms} stands outside any custom tag - it declares the parameters of one, in its template" );
    return NULL;
  }

  padParmsDeclare ( $padParmsOwner );

  return NULL;

?>

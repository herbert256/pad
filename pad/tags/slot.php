<?php

  // {slot 'footer'}default{/slot} in a custom tag's template: where the caller's fill for
  // that slot goes - lib/slot.php. A {slot} pair standing directly in the content of a
  // custom tag is a fill instead; the tag's level took those out before this runs.
  //
  // Filled, the fill renders here; when the default holds @content@, the default is a
  // frame for the fill - {slot 'footer'}<footer>@content@</footer>{/slot} - and the frame
  // only renders when there is something to put in it. Not filled, the default renders,
  // and a frame with nothing to frame gives way to its @else@ part, or to nothing.

  if ( trim ( (string) $padParm ) === '' ) {
    if ( $padCheckSyntax )
      padError ( "the {slot} needs a name - {slot 'footer'}" );
    return NULL;
  }

  $padSlotOwner = padSlotOwner ();

  if ( $padSlotOwner === FALSE ) {
    if ( $padCheckSyntax )
      padError ( "the {slot '$padParm'} stands outside any custom tag - a slot belongs in the template of one" );
    return NULL;
  }

  $padSlotFill  = padSlotFill ( $padParm, $padSlotOwner );
  $padSlotFrame = padContentBeforeAfter ( $padContent, $padSlotBefore, $padSlotAfter );

  if ( $padSlotFill === NULL )
    return ! $padSlotFrame;

  $padSlotFrom [$pad] = $padSlotOwner;

  $padContent = $padSlotFrame ? $padSlotBefore . $padSlotFill . $padSlotAfter : $padSlotFill;

  return TRUE;

?>

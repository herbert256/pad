<?php

  // What the at-type lines read: a global for @all, and one for the saved group to shadow.
  //
  // The last three lines pin what does not work, on purpose. The level and function groups
  // answer nothing for any spelling tried; the data@ property resolves since the audit's
  // padAtSetTag repair - it no longer crashes - but no template spelling brings its values
  // out, so the line still asserts the empty answer, guarded by optional. The day one of
  // them starts answering, this page reports the change.

  $catAll   = 'found';
  $catSaved = 'outer';

?>

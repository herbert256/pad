<?php

  // {section 'hero', title='Ship pages, not plumbing', button='Start', link='?start'} ...
  // {/section} - a block of a landing page of the kind the first parameter names, a file of
  // subs/sections/types/: the same section as the section: type writes,
  // {section:hero title='...'} ... {/section:hero}. The section's own first value - the
  // heading of {section 'cta', 'Ready?'}, the name of an item - is the second here.
  // subs/sections/section.php, on every walk the section asks for.

  $padSectionKind = is_string ( $padParm ) ? strtolower ( trim ( $padParm ) ) : '';

  return include PAD . 'subs/sections/section.php';

?>

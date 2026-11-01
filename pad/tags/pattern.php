<?php

  // {pattern 'topography', seed=7/} - a seeded SVG background or piece of generative art, drawn
  // on the server by the pattern subsystem in subs/patterns/: a kind is a file of
  // subs/patterns/types/, the same pattern as the pattern: type writes it,
  // {pattern:topography seed=7/}. The same options give the same bytes on every request.
  //
  //   {pattern 'dots', size=24, colors='#fdfbf7, #e0c9a6'/}
  //   <div style="background-image:{pattern 'waves', as='url'/}">...</div>
  //
  // padPattern ( 'topography', [ 'seed' => 7 ] ) draws the same in a page's PHP.

  $padPatternKind = strtolower ( trim ( (string) $padParm ) );

  return include PAD . 'subs/patterns/pattern.php';

?>

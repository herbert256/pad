<?php

  // Six islands, three ways of loading: the first two at once, two when the browser is idle,
  // two only when they scroll into view - far down the page. The log is an island too, loaded
  // at once so it hears the others mount.

  $loads = [ [ 'island' => 'ReactCounter',  'load' => 'eager',   'framework' => 'React',  'start' => 1 ],
             [ 'island' => 'PreactCounter', 'load' => 'eager',   'framework' => 'Preact', 'start' => 2 ],
             [ 'island' => 'VueCounter',    'load' => 'idle',    'framework' => 'Vue',    'start' => 3 ],
             [ 'island' => 'SolidCounter',  'load' => 'idle',    'framework' => 'Solid',  'start' => 4 ],
             [ 'island' => 'SvelteCounter', 'load' => 'visible', 'framework' => 'Svelte', 'start' => 5 ] ];

  foreach ( $loads as $at => $one )
    $loads [$at] ['props'] = [ 'start' => $one ['start'], 'framework' => $one ['framework'] ];

?>

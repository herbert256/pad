<?php

  // One counter, five frameworks: each island gets its start value and its framework's name
  // from PAD, and the card beside it what the island costs - the bytes of its component, its
  // framework's adapter and every chunk they import, read from the build's manifest.

  $counters = [];

  foreach ( [ [ 'React',  'ReactCounter',  'src/islands/Counter.react.jsx',  'src/adapters/react.js',  '#61dafb' ],
              [ 'Vue',    'VueCounter',    'src/islands/Counter.vue',        'src/adapters/vue.js',    '#42b883' ],
              [ 'Svelte', 'SvelteCounter', 'src/islands/Counter.svelte',     'src/adapters/svelte.js', '#ff3e00' ],
              [ 'Preact', 'PreactCounter', 'src/islands/Counter.preact.js',  'src/adapters/preact.js', '#673ab8' ],
              [ 'Solid',  'SolidCounter',  'src/islands/Counter.solid.jsx',  'src/adapters/solid.js',  '#2c4f7c' ] ]
            as $at => [ $framework, $island, $component, $adapter, $colour ] )

    $counters [] = [ 'framework' => $framework,
                     'island'    => $island,
                     'colour'    => $colour,
                     'kilobytes' => number_format ( islandsWeight ( [ $component, $adapter ] ) / 1024, 1 ),
                     'props'     => [ 'start' => 10 * ( $at + 1 ), 'framework' => $framework ] ];

?>

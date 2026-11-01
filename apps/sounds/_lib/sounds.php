<?php

  // The sounds of the engine, as the engine reads them - the files of pad/subs/sounds/types/
  // (padSoundKinds) - each with what its own file says of it: the comment at its top, its
  // line the first sentence after the name and the dash, and the recipe itself, as it stands
  // in the file, coloured. A list, not keyed by the sounds' names.

  function soundsCatalog () {

    include_once PAD . 'subs/sounds/_lib/sound.php';

    $sounds = [];

    foreach ( padSoundKinds () as $kind ) {

      $file    = (string) padFileGet ( PAD . "subs/sounds/types/$kind.php" );
      $comment = preg_match ( '/\A<\?php\s*\n((?:[ \t]*\/\/[^\n]*\n)+)/', $file, $m )
               ? trim ( implode ( ' ', array_map ( fn ( $line ) => trim ( preg_replace ( '/^\s*\/\/ ?/', '', $line ) ), explode ( "\n", trim ( $m [1] ) ) ) ) )
               : '';
      $text    = ucfirst ( preg_replace ( '/^' . preg_quote ( $kind, '/' ) . '\s+-\s+/', '', $comment ) );
      $first   = preg_split ( '/(?<=[.:])\s/', $text, 2 ) [0];
      $recipe  = padSoundRecipe ( $kind );
      $code    = trim ( preg_replace ( '/\A<\?php\s*\n(?:[ \t]*\/\/[^\n]*\n)+\s*/', '', preg_replace ( '/\s*\?>\s*\z/', '', $file ) ) );

      $sounds [] = [ 'kind'    => $kind,
                     'name'    => $recipe ['title'],
                     'line'    => rtrim ( $first, ':.' ) . '.',
                     'text'    => $text,
                     'seconds' => number_format ( $recipe ['length'], 2 ),
                     'voices'  => count ( $recipe ['voices'] ),
                     'recipe'  => padHighlightTokens ( $code, 'php' ) ];

    }

    return $sounds;

  }

  // The ways to play a sound, each a page of ways/: its title and what it shows.

  function soundsWays () {

    return [
      'button'   => [ 'A button',                'The tag writes a real button that plays the sound - named after the sound, or by button=.' ],
      'target'   => [ 'On an element of the page', 'target= finds elements by a CSS selector and plays the sound on their event - on= click, pointerdown, hover, focus, change, input or submit.' ],
      'attribute'=> [ 'Any element',              'Once a sound has put the script on the page, data-pad-sound on any element plays that sound when it is clicked - data-pad-sound-on for another event.' ],
      'toast'    => [ 'A toast that sounds',      'sound= on a toast plays a sound as it shows - after a gesture on the page, as browsers allow.' ],
      'download' => [ 'The WAV file',             'download links the sound as a WAV file: the page asked again with padSound, answered by the same recipe the browser plays.' ],
    ];

  }

?>

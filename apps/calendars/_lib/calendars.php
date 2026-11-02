<?php

  // The engine owns the list of views; the showcase reads each kind's own description.
  function calendarsCatalog () {
    $views = [];
    foreach ( padKinds ( 'calendars' ) as $kind ) {
      $source = file_get_contents ( PAD . "subs/calendars/types/$kind.php" );
      preg_match ( '/^\s*\/\/ ' . preg_quote ( $kind, '/' ) . ' - (.*)$/m', $source, $match );
      $views [$kind] = [ 'kind' => $kind, 'name' => ucfirst ( $kind ), 'about' => ucfirst ( $match [1] ?? $kind ),
                         'tag' => "{calendar:$kind/}" ];
    }
    return $views;
  }

?>

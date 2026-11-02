<?php

  $walks = [
    [ 'naam' => 'Langs de dijken', 'over' => 'Fietsen langs de rivier en door de polders.' ],
    [ 'naam' => 'Door de bossen',  'over' => 'Wandelingen en fietstochten door de bossen.' ],
    [ 'naam' => 'Aan zee',         'over' => 'Wandelen over het strand, de wind in de rug.' ]
  ];

  $found = padSearch ( $walks, 'wandeling', [ 'name' => 'walks', 'lang' => 'nl', 'fields' => 'naam^2, over' ] );

?>

<?php

  // A duration without its unit, a step without its text and an ingredient that is a row:
  // errors under the strict check, left out without it - the rest of the recipe written.

  $padCheckSyntax = FALSE;

  $soup = [ 'name' => 'Soup', 'image' => 'soup.jpg', 'prep' => '20', 'cook' => '1 h',
            'ingredients' => [ 'peas', [ 'name' => 'water' ] ],
            'steps' => [ [ 'name' => 'Only a name' ], 'Stir.' ] ];

?>

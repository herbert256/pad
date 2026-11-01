<?php

  // A recipe as a database answers it: its lists as lists, a step a row of its own - whose
  // cost, like the cost of the recipe, never reaches the page - and an ingredient left empty.

  $soup = [ 'name' => 'Pea soup', 'image' => [ 'soup-1x1.jpg', 'https://cdn.example/soup-16x9.jpg' ],
            'author' => 'Ann Smith', 'datePublished' => '2026-11-01', 'prep' => '20 min', 'cook' => '1.5 hours',
            'yield' => '4 servings', 'category' => 'Dinner', 'cuisine' => 'Dutch', 'keywords' => 'winter, hearty',
            'calories' => 320, 'ingredients' => [ '500 g split peas', '1 leek, sliced', '', '2 l water' ],
            'steps' => [ 'Soak the peas.', [ 'name' => 'Simmer', 'text' => 'Simmer for an hour.', 'url' => '?soup#simmer', 'cost' => 3 ] ],
            'rating' => 4.6, 'ratings' => 18, 'cost' => '2.10' ];

?>

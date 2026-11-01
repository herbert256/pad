<?php

  // {jsonld:recipe data='recipe'/} or {jsonld:recipe name='Pea soup', image='soup.jpg',
  // prep='20 min', cook='1 h 30 min', yield='4 servings'/} - a Recipe: a dish, what goes in
  // it, how it is made and how long that takes.
  //
  // The fields, by their PAD name and the Schema.org property each fills - the required and
  // recommended properties of Google's recipe rich result: name (name, required), image
  // (image, required - one address or more, a list or a text with a comma between them:
  // Google asks for a 16x9, a 4x3 and a 1x1), description (description), author (author, a
  // Person by its name), published (datePublished, a date), prep (prepTime), cook
  // (cookTime) and total (totalTime) - durations, PT20M or 20 min - yield (recipeYield, the
  // servings: 4, 4 servings), category (recipeCategory, dinner, dessert), cuisine
  // (recipeCuisine, French), keywords (keywords, a text with a comma between them),
  // calories (nutrition.calories, the energy of a serving, 120 calories - a number alone is
  // that many), ingredients (recipeIngredient, a list in the row, or a text with a line
  // between them), steps (recipeInstructions, a HowToStep each: a list of texts in the row,
  // a text with a line between them, or rows of text, name, url and image - nothing else of
  // such a row), rating and ratings (aggregateRating, the
  // average score and the number of ratings - the one no use without the other). Calories
  // need the yield, as Google asks: the energy is that of a serving.

  return [

    'type'    => 'Recipe',

    'fields'  => [
      'name'        => [ 'name',                          'text',     TRUE ],
      'image'       => [ 'image',                         'urls',     TRUE ],
      'description' => [ 'description',                   'text'           ],
      'author'      => [ 'author.name',                   'text'           ],
      'published'   => [ 'datePublished',                 'date'           ],
      'prep'        => [ 'prepTime',                      'duration'       ],
      'cook'        => [ 'cookTime',                      'duration'       ],
      'total'       => [ 'totalTime',                     'duration'       ],
      'yield'       => [ 'recipeYield',                   'text'           ],
      'category'    => [ 'recipeCategory',                'text'           ],
      'cuisine'     => [ 'recipeCuisine',                 'text'           ],
      'keywords'    => [ 'keywords',                      'text'           ],
      'calories'    => [ 'nutrition.calories',            'energy'         ],
      'ingredients' => [ 'recipeIngredient',              'texts'          ],
      'steps'       => [ 'recipeInstructions',            'steps'          ],
      'rating'      => [ 'aggregateRating.ratingValue',   'number'         ],
      'ratings'     => [ 'aggregateRating.ratingCount',   'count'          ],
    ],

    'objects' => [ 'author' => 'Person', 'nutrition' => 'NutritionInformation', 'aggregateRating' => 'AggregateRating' ],

    'needs'   => [ 'calories' => 'yield', 'rating' => 'ratings', 'ratings' => 'rating' ],

  ];

?>

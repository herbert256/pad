<?php

  $words = [
    'item' => 'items', 'city' => 'cities', 'day' => 'days', 'box' => 'boxes', 'class' => 'classes',
    'church' => 'churches', 'dish' => 'dishes', 'waltz' => 'waltzes', 'buzz' => 'buzzes',
    'person' => 'people', 'child' => 'children', 'man' => 'men', 'woman' => 'women',
    'mouse' => 'mice', 'goose' => 'geese', 'foot' => 'feet', 'tooth' => 'teeth', 'ox' => 'oxen',
    'quiz' => 'quizzes', 'knife' => 'knives', 'wife' => 'wives', 'leaf' => 'leaves',
    'shelf' => 'shelves', 'roof' => 'roofs', 'chief' => 'chiefs', 'hero' => 'heroes',
    'potato' => 'potatoes', 'photo' => 'photos', 'analysis' => 'analyses', 'crisis' => 'crises',
    'hypothesis' => 'hypotheses', 'status' => 'statuses', 'bus' => 'buses', 'cactus' => 'cacti',
    'criterion' => 'criteria', 'index' => 'indices', 'human' => 'humans', 'specimen' => 'specimens',
    'movie' => 'movies', 'house' => 'houses', 'database' => 'databases', 'cache' => 'caches',
    'beach' => 'beaches', 'size' => 'sizes', 'stomach' => 'stomachs', 'grandchild' => 'grandchildren',
    'salesperson' => 'salespeople', 'gas' => 'gases', 'olive' => 'olives', 'glove' => 'gloves'
  ];

  $wrong = [];

  foreach ( $words as $one => $many )
    if ( padStrPlural ( $one ) !== $many or padStrSingular ( $many ) !== $one
      or padStrPlural ( $many ) !== $many or padStrSingular ( $one ) !== $one )
      $wrong [] = $one;

  $r = json_encode ( [ count ( $words ), $wrong ] );

  $uncountable = [];

  foreach ( [ 'sheep', 'fish', 'goldfish', 'series', 'species', 'information', 'equipment', 'news',
              'money', 'rice', 'metadata', 'software', 'userInformation' ] as $word )
    if ( padStrPlural ( $word ) !== $word or padStrSingular ( $word ) !== $word )
      $uncountable [] = $word;

  $u = json_encode ( [ $uncountable, padStrPlural ( 'price' ), padStrSingular ( 'prices' ) ] );

  $c = json_encode ( [
    padStrPlural ( 'Person' ), padStrPlural ( 'PERSON' ), padStrSingular ( 'People' ), padStrSingular ( 'PEOPLE' ),
    padStrPlural ( 'Child' ), padStrPlural ( 'City' ), padStrPlural ( 'blog post' ), padStrPlural ( 'blogPost' ),
    padStrPlural ( 'salesPerson' ), padStrSingular ( 'Blog Posts' ), padStrPlural ( 'İstanbul' ), padStrPlural ( 'cat ' )
  ], JSON_UNESCAPED_UNICODE );

  $n = json_encode ( [
    padStrPlural ( 'file', 1 ), padStrPlural ( 'file', -1 ), padStrPlural ( 'file', 0 ), padStrPlural ( 'file', 2 ),
    padStrPlural ( 'file', '1' ), padStrPlural ( 'file', 1.0 ), padStrPlural ( 'file', 1.5 ),
    padStrPlural ( 'file', [ 'a.txt' ] ), padStrPlural ( 'file', [ 'a.txt', 'b.txt' ] ),
    padStrPlural ( 'file', new ArrayObject ( [] ) ),
    padStrPlural ( '' ), padStrPlural ( NULL ), padStrPlural ( '42' ), padStrSingular ( '' ), padStrSingular ( NULL )
  ] );

?>

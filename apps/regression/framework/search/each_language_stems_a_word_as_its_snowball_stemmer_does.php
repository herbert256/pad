<?php

  // Words of Snowball's own test vocabularies with the stems its output gives them - the
  // five stemmers match every word of those vocabularies (42649 English, 45670 Dutch, 35053
  // German, 21653 French, 28378 Spanish).

  include_once PAD . 'subs/search/_lib/search.php';

  $stems = [];

  foreach ( [ 'en' => 'running runs generously traditional happiness skies dying proceeding hopefulness communication agreed tanned',
              'nl' => 'maken huisjes gelopen opgaven koninklijke aanvragen mogelijkheden boeken fietsen fiets',
              'de' => 'häuser aufeinanderfolgenden kategorien schönheit abenteuerlich mädchen gesprochen zeitungen freundlichkeit möglichkeiten',
              'fr' => "continuellement nationales abandonnés chanteuses heureusement majestueuse chevaux générosité l'homme finissait",
              'es' => 'canciones rápidamente dándole informaciones nacionalidad corriendo trabajadores organización ciudades lentamente' ] as $lang => $words ) {

    $language = padSearchLanguage ( $lang );
    $line     = [];

    foreach ( explode ( ' ', $words ) as $word )
      $line [] = "$word=" . ( $language ['stem'] ) ( $word );

    $stems [] = "$lang: " . implode ( ' ', $line );

  }

  $stems = implode ( ' | ', $stems );

?>

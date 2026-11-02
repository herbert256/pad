<?php

  // Samples of Snowball's Swedish, Danish and Norwegian test vocabularies - 16 words of each
  // evenly spaced, and words of each step: the et_condition (frihet, paket and societet keep
  // their et), -lig, the apostrophe of pc'en - with the stems its output gives them; the
  // three stemmers match every word of them. The locale nb_NO searches in Norwegian.

  include_once PAD . 'subs/search/_lib/search.php';

  $stems = [];

  foreach ( [ 'sv' => 'attityd bordel eggas fylld glidit huvudkudde kaskader levins mossan orimlig riktning skrivelsen stormodighet
                       tjenstflicka utgångna äktenskapliga flickorna husets tidningarna tydligt frihet paket societet',
              'da' => "ammonit bjerge dyrkning fordømmes genløst herskede isreaeliter krogene mandelgren næstøverste prisgive sejrsråb
                       spiseligt tankeløs udlægning vognspor husene hurtigt venlighed pc'en børnene lykkelig",
              'no' => 'armringer brualternativet eig fordelingsreglene generalforsamlingaallmenne hovedkriterium kalde landeveg
                       merkesystemet oppfattar profiler samferdselsnett snøret temmet uinnfridde vesenene husene barna kommunene
                       hyggelig' ] as $lang => $words ) {

    $language = padSearchLanguage ( $lang );
    $line     = [];

    foreach ( preg_split ( '/\s+/', $words ) as $word )
      $line [] = "$word=" . ( $language ['stem'] ) ( $word );

    $stems [] = "$lang: " . implode ( ' ', $line );

  }

  $stems = implode ( ' | ', $stems );

  $padLocale = 'nb_NO';

?>

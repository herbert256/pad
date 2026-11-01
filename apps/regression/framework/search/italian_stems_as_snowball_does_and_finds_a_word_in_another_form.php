<?php

  // A sample of Snowball's Italian test vocabulary - 24 of its 37426 words evenly spaced, and
  // words of each step: elisions, an attached pronoun, -amente, -ità, -azione, -logia, -che
  // and divano - with the stems its output gives them; the stemmer matches every word of it.

  include_once PAD . 'subs/search/_lib/search.php';

  $language = padSearchLanguage ( 'it' );
  $stems    = [];

  foreach ( preg_split ( '/\s+/', "affacciaron appagherò badò campobasso colui coronato dell'orchestra droga fenomeno giornalistici
                                   incomodare istoriando lievitare minacciava odiosità perone procede regolarmente rispettivi
                                   schivafatiche soffrivano stringer traluce versamento l'università dell'anno all'estero abbandonata
                                   portarlo amiche divano rapidamente possibilità comunicazione biologia" ) as $word )
    $stems [] = "$word=" . ( $language ['stem'] ) ( $word );

  $stems = implode ( ' ', $stems );

?>

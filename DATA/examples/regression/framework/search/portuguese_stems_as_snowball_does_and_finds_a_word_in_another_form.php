<?php

  // A sample of Snowball's Portuguese test vocabulary - 24 of its 32016 words evenly spaced,
  // and words of each step: -ações, -mente, -idade, -logia, -eira - with the stems its output
  // gives them; the stemmer matches every word of it.

  include_once PAD . 'subs/search/_lib/search.php';

  $language = padSearchLanguage ( 'pt' );
  $stems    = [];

  foreach ( preg_split ( '/\s+/', 'adoçante apegados ayres cães cinzentos constestador declararam diasa empurrou esteve finalidades
                                   gourmet incomodasse judeu maioria moluscos ondulações pernambuco produtivo refletor saia spitzer
                                   tórrido vias informações rapidamente brasileira cidades nações portuguesa possibilidade
                                   sociologia brasileiro felizmente' ) as $word )
    $stems [] = "$word=" . ( $language ['stem'] ) ( $word );

  $stems = implode ( ' ', $stems );

?>

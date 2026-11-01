<?php

  // A sample of Snowball's Russian test vocabulary - 24 of its 49785 words evenly spaced, and
  // words of each step: ё read as е, a perfective gerund, an adjective, a superlative, -ость
  // - with the stems its output gives them; the stemmer matches every word of it.

  include_once PAD . 'subs/search/_lib/search.php';

  $language = padSearchLanguage ( 'ru' );
  $stems    = [];

  foreach ( preg_split ( '/\s+/', 'бекешах верует встретился градуса дремные зальет измерит корзину маленького назначать непременною
                                   оглянулся отпускалось плясать полуформенной представлю проклятиями разрушаешь свидетельство
                                   снилась стройные трясясь утопающей шарманку книги красивыми беднейшие берёза ещё всё длинный
                                   читая вернувшись взявши бедность' ) as $word )
    $stems [] = "$word=" . ( $language ['stem'] ) ( $word );

  $stems = implode ( ' ', $stems );

?>

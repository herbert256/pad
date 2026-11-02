<?php

  // The month and the weekdays in the language of the page's locale, as {pad:calendar} writes
  // them (ICU's LLLL uuuu and EEE), and the names of the holidays in it too when the country
  // speaks it: nl_NL - april 2027, ma di wo, Koningsdag - where de_DE has German months and
  // days, and the English names of the Dutch holidays, King's Day: German is no language of
  // the Netherlands.

  $padLocale = 'nl_NL';
  $answer    = calendarShow ( "{printable:calendar month='2027-04', holidays='nl'/}" );
  $padLocale = 'de_DE';
  $answer   .= ' ## ' . calendarShow ( "{printable:calendar month='2027-04', holidays='nl'/}" );

?>

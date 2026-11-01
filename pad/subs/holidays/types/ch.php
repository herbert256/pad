<?php

  // ch - Switzerland: the four holidays every canton keeps - New Year's Day, Ascension Day,
  // the Swiss National Day (1 August, the federal holiday by the constitution) and Christmas
  // Day - their names in German, French and Italian, lang= picks one, German the default.
  // Every other holiday - Good Friday, Easter and Whit Monday, St Stephen's Day, Berchtold's
  // Day, Labour Day, Corpus Christi, the Jeûne genevois - is the cantons' to declare, up to
  // eight each, and differs between them: the cantons are left out rather than given half
  // right, so a workday count for Zurich still counts its Good Friday.

  return [
    'name'  => 'Switzerland',
    'langs' => [ 'de', 'fr', 'it' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",   'local' => [ 'de' => 'Neujahrstag', 'fr' => 'Nouvel An',      'it' => 'Capodanno' ] ],
      [ 'easter' => 39,      'name' => 'Ascension Day',    'local' => [ 'de' => 'Auffahrt',    'fr' => 'Ascension',      'it' => 'Ascensione' ] ],
      [ 'date'   => '08-01', 'name' => 'Swiss National Day', 'local' => [ 'de' => 'Bundesfeier', 'fr' => 'Fête nationale', 'it' => 'Festa nazionale' ] ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',    'local' => [ 'de' => 'Weihnachten', 'fr' => 'Noël',           'it' => 'Natale' ] ]
    ]
  ];

?>

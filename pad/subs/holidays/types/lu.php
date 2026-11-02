<?php

  // lu - Luxembourg: the eleven legal public holidays, Europe Day (9 May) since 2019, their
  // names in Luxembourgish, French and German - lang= picks one, Luxembourgish the default. A
  // holiday on a Sunday gives a day off to be taken within three months, not a fixed date, so
  // no substitute is given.

  return [
    'name'  => 'Luxembourg',
    'langs' => [ 'lb', 'fr', 'de' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",    'local' => [ 'lb' => 'Neijoerschdag',       'fr' => "Jour de l'An",       'de' => 'Neujahr' ] ],
      [ 'easter' => 1,       'name' => 'Easter Monday',     'local' => [ 'lb' => 'Ouschterméindeg',     'fr' => 'Lundi de Pâques',    'de' => 'Ostermontag' ] ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',        'local' => [ 'lb' => 'Dag vun der Aarbecht', 'fr' => 'Fête du Travail',   'de' => 'Tag der Arbeit' ] ],
      [ 'date'   => '05-09', 'name' => 'Europe Day',        'local' => [ 'lb' => 'Europadag',           'fr' => "Journée de l'Europe", 'de' => 'Europatag' ], 'from' => 2019 ],
      [ 'easter' => 39,      'name' => 'Ascension Day',     'local' => [ 'lb' => 'Christi Himmelfaart', 'fr' => 'Ascension',          'de' => 'Christi Himmelfahrt' ] ],
      [ 'easter' => 50,      'name' => 'Whit Monday',       'local' => [ 'lb' => 'Péngschtméindeg',     'fr' => 'Lundi de Pentecôte', 'de' => 'Pfingstmontag' ] ],
      [ 'date'   => '06-23', 'name' => 'National Day',      'local' => [ 'lb' => 'Nationalfeierdag',    'fr' => 'Fête nationale',     'de' => 'Nationalfeiertag' ] ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day',    'local' => [ 'lb' => 'Léiffrawëschdag',     'fr' => 'Assomption',         'de' => 'Mariä Himmelfahrt' ] ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",   'local' => [ 'lb' => 'Allerhellgen',        'fr' => 'Toussaint',          'de' => 'Allerheiligen' ] ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',     'local' => [ 'lb' => 'Chrëschtdag',         'fr' => 'Noël',               'de' => 'Weihnachten' ] ],
      [ 'date'   => '12-26', 'name' => "St Stephen's Day",  'local' => [ 'lb' => 'Stiefesdag',          'fr' => 'Saint-Étienne',      'de' => 'Stephanstag' ] ]
    ]
  ];

?>

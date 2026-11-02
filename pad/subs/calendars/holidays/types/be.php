<?php

  // be - Belgium: the ten legal public holidays (wettelijke feestdagen, jours fériés légaux),
  // their names in Dutch, French and German - lang= picks one, Dutch the default. A holiday on
  // a Sunday is made up for by a day the employer or the sector chooses, so there is no
  // substitute date to give. The days of the Flemish, French and German-speaking Communities
  // (11 July, 27 September, 15 November) are days off for their civil services only and are
  // left out.

  return [
    'name'  => 'Belgium',
    'langs' => [ 'nl', 'fr', 'de' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day", 'local' => [ 'nl' => 'Nieuwjaar',                   'fr' => "Jour de l'An",          'de' => 'Neujahr' ] ],
      [ 'easter' => 1,       'name' => 'Easter Monday',  'local' => [ 'nl' => 'Paasmaandag',                 'fr' => 'Lundi de Pâques',       'de' => 'Ostermontag' ] ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',     'local' => [ 'nl' => 'Dag van de Arbeid',           'fr' => 'Fête du Travail',       'de' => 'Tag der Arbeit' ] ],
      [ 'easter' => 39,      'name' => 'Ascension Day',  'local' => [ 'nl' => 'Onze-Lieve-Heer-Hemelvaart',  'fr' => 'Ascension',             'de' => 'Christi Himmelfahrt' ] ],
      [ 'easter' => 50,      'name' => 'Whit Monday',    'local' => [ 'nl' => 'Pinkstermaandag',             'fr' => 'Lundi de Pentecôte',    'de' => 'Pfingstmontag' ] ],
      [ 'date'   => '07-21', 'name' => 'National Day',   'local' => [ 'nl' => 'Nationale feestdag',          'fr' => 'Fête nationale',        'de' => 'Nationalfeiertag' ] ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day', 'local' => [ 'nl' => 'Onze-Lieve-Vrouw-Hemelvaart', 'fr' => 'Assomption',            'de' => 'Mariä Himmelfahrt' ] ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day", 'local' => [ 'nl' => 'Allerheiligen',              'fr' => 'Toussaint',             'de' => 'Allerheiligen' ] ],
      [ 'date'   => '11-11', 'name' => 'Armistice Day',  'local' => [ 'nl' => 'Wapenstilstand',              'fr' => 'Armistice',             'de' => 'Waffenstillstand' ] ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',  'local' => [ 'nl' => 'Kerstmis',                    'fr' => 'Noël',                  'de' => 'Weihnachten' ] ]
    ]
  ];

?>

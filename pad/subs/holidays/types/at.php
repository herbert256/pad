<?php

  // at - Austria: the thirteen public holidays of the Feiertagsruhegesetz, the same in every
  // state. Good Friday has been no public holiday since 2019 (before, for some churches'
  // members only) and is left out; so are the states' patron saints' days, school holidays
  // rather than days off.

  return [
    'name'  => 'Austria',
    'langs' => [ 'de' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",         'local' => 'Neujahr' ],
      [ 'date'   => '01-06', 'name' => 'Epiphany',               'local' => 'Heilige Drei Könige' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',          'local' => 'Ostermontag' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',             'local' => 'Staatsfeiertag' ],
      [ 'easter' => 39,      'name' => 'Ascension Day',          'local' => 'Christi Himmelfahrt' ],
      [ 'easter' => 50,      'name' => 'Whit Monday',            'local' => 'Pfingstmontag' ],
      [ 'easter' => 60,      'name' => 'Corpus Christi',         'local' => 'Fronleichnam' ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day',         'local' => 'Mariä Himmelfahrt' ],
      [ 'date'   => '10-26', 'name' => 'National Day',           'local' => 'Nationalfeiertag' ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",        'local' => 'Allerheiligen' ],
      [ 'date'   => '12-08', 'name' => 'Immaculate Conception',  'local' => 'Mariä Empfängnis' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',          'local' => 'Christtag' ],
      [ 'date'   => '12-26', 'name' => "St Stephen's Day",       'local' => 'Stefanitag' ]
    ]
  ];

?>

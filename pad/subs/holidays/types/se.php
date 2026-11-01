<?php

  // se - Sweden: the public holidays of the Act on General Holidays (lag 1989:253), Easter and
  // Whit Sunday among them; the National Day, 6 June, since 2005, when it replaced Whit
  // Monday. Midsummer Day is the Saturday from 20 to 26 June, All Saints' Day the Saturday
  // from 31 October to 6 November. Midsummer Eve, Christmas Eve and New Year's Eve close the
  // banks and are days off by most agreements, but are no public holidays by law: they are
  // listed as observances.

  return [
    'name'  => 'Sweden',
    'langs' => [ 'sv' ],
    'days'  => [
      [ 'date'      => '01-01',             'name' => "New Year's Day",    'local' => 'Nyårsdagen' ],
      [ 'date'      => '01-06',             'name' => 'Epiphany',          'local' => 'Trettondedag jul' ],
      [ 'easter'    => -2,                  'name' => 'Good Friday',       'local' => 'Långfredagen' ],
      [ 'easter'    => 0,                   'name' => 'Easter Sunday',     'local' => 'Påskdagen' ],
      [ 'easter'    => 1,                   'name' => 'Easter Monday',     'local' => 'Annandag påsk' ],
      [ 'date'      => '05-01',             'name' => 'May Day',           'local' => 'Första maj' ],
      [ 'easter'    => 39,                  'name' => 'Ascension Day',     'local' => 'Kristi himmelsfärdsdag' ],
      [ 'easter'    => 49,                  'name' => 'Whit Sunday',       'local' => 'Pingstdagen' ],
      [ 'easter'    => 50,                  'name' => 'Whit Monday',       'local' => 'Annandag pingst', 'to' => 2004 ],
      [ 'date'      => '06-06',             'name' => 'National Day',      'local' => 'Sveriges nationaldag', 'from' => 2005 ],
      [ 'onOrAfter' => [ 'fri', '06-19' ],  'name' => 'Midsummer Eve',     'local' => 'Midsommarafton', 'type' => 'observance' ],
      [ 'onOrAfter' => [ 'sat', '06-20' ],  'name' => 'Midsummer Day',     'local' => 'Midsommardagen' ],
      [ 'onOrAfter' => [ 'sat', '10-31' ],  'name' => "All Saints' Day",   'local' => 'Alla helgons dag' ],
      [ 'date'      => '12-24',             'name' => 'Christmas Eve',     'local' => 'Julafton', 'type' => 'observance' ],
      [ 'date'      => '12-25',             'name' => 'Christmas Day',     'local' => 'Juldagen' ],
      [ 'date'      => '12-26',             'name' => 'Boxing Day',        'local' => 'Annandag jul' ],
      [ 'date'      => '12-31',             'name' => "New Year's Eve",    'local' => 'Nyårsafton', 'type' => 'observance' ]
    ]
  ];

?>

<?php

  // it - Italy: the national public holidays (giorni festivi), Easter Sunday among them. The
  // Festa della Repubblica is 2 June since 2001 - from 1977 to 2000 it was kept on the first
  // Sunday of June; St Francis of Assisi, the patron saint of Italy, 4 October, is a national
  // holiday again since 2026. The patron saint of each town (Rome's 29 June, Milan's
  // 7 December) and South Tyrol's Whit Monday are local and left out.

  return [
    'name'  => 'Italy',
    'langs' => [ 'it' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",       'local' => 'Capodanno' ],
      [ 'date'   => '01-06', 'name' => 'Epiphany',             'local' => 'Epifania' ],
      [ 'easter' => 0,       'name' => 'Easter Sunday',        'local' => 'Pasqua' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',        'local' => "Lunedì dell'Angelo" ],
      [ 'date'   => '04-25', 'name' => 'Liberation Day',       'local' => 'Festa della Liberazione' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',           'local' => 'Festa del Lavoro' ],
      [ 'date'   => '06-02', 'name' => 'Republic Day',         'local' => 'Festa della Repubblica', 'to' => 1976 ],
      [ 'nth'    => [ 1, 'sun', 6 ], 'name' => 'Republic Day', 'local' => 'Festa della Repubblica', 'from' => 1977, 'to' => 2000 ],
      [ 'date'   => '06-02', 'name' => 'Republic Day',         'local' => 'Festa della Repubblica', 'from' => 2001 ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day',       'local' => 'Ferragosto' ],
      [ 'date'   => '10-04', 'name' => 'St Francis of Assisi', 'local' => "San Francesco d'Assisi", 'from' => 2026 ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",      'local' => 'Ognissanti' ],
      [ 'date'   => '12-08', 'name' => 'Immaculate Conception', 'local' => 'Immacolata Concezione' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',        'local' => 'Natale' ],
      [ 'date'   => '12-26', 'name' => "St Stephen's Day",     'local' => 'Santo Stefano' ]
    ]
  ];

?>

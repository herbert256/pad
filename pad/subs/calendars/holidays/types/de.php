<?php

  // de - Germany: the nine holidays of every state, and with region= a state's own, by its ISO
  // 3166-2 code - de-by is Bavaria. Exact since 1995, when Repentance Day became Saxony's
  // alone; Reformation Day is a holiday of the five eastern states since 1990, of Bremen,
  // Hamburg, Lower Saxony and Schleswig-Holstein since 2018, and was one of all Germany in
  // 2017; Women's Day is Berlin's since 2019 and Mecklenburg-Vorpommern's since 2023,
  // World Children's Day Thuringia's since 2019. Left out: the holidays of single towns
  // (Augsburg's Peace Festival) and of the Catholic communities of Bavaria (Assumption Day),
  // Saxony and Thuringia (Corpus Christi).

  return [
    'name'    => 'Germany',
    'langs'   => [ 'de' ],
    'regions' => [ 'bw' => 'Baden-Württemberg', 'by' => 'Bavaria', 'be' => 'Berlin', 'bb' => 'Brandenburg',
                   'hb' => 'Bremen', 'hh' => 'Hamburg', 'he' => 'Hesse', 'mv' => 'Mecklenburg-Western Pomerania',
                   'ni' => 'Lower Saxony', 'nw' => 'North Rhine-Westphalia', 'rp' => 'Rhineland-Palatinate',
                   'sl' => 'Saarland', 'sn' => 'Saxony', 'st' => 'Saxony-Anhalt', 'sh' => 'Schleswig-Holstein',
                   'th' => 'Thuringia' ],
    'days'    => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",          'local' => 'Neujahr' ],
      [ 'date'   => '01-06', 'name' => 'Epiphany',                'local' => 'Heilige Drei Könige', 'in' => [ 'bw', 'by', 'st' ] ],
      [ 'date'   => '03-08', 'name' => "International Women's Day", 'local' => 'Internationaler Frauentag', 'in' => [ 'be' ], 'from' => 2019 ],
      [ 'date'   => '03-08', 'name' => "International Women's Day", 'local' => 'Internationaler Frauentag', 'in' => [ 'mv' ], 'from' => 2023 ],
      [ 'easter' => -2,      'name' => 'Good Friday',             'local' => 'Karfreitag' ],
      [ 'easter' => 0,       'name' => 'Easter Sunday',           'local' => 'Ostersonntag', 'in' => [ 'bb' ] ],
      [ 'easter' => 1,       'name' => 'Easter Monday',           'local' => 'Ostermontag' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',              'local' => 'Tag der Arbeit' ],
      [ 'date'   => '05-08', 'name' => 'Liberation Day',          'local' => 'Tag der Befreiung', 'in' => [ 'be' ], 'from' => 2020, 'to' => 2020 ],
      [ 'date'   => '05-08', 'name' => 'Liberation Day',          'local' => 'Tag der Befreiung', 'in' => [ 'be' ], 'from' => 2025, 'to' => 2025 ],
      [ 'easter' => 39,      'name' => 'Ascension Day',           'local' => 'Christi Himmelfahrt' ],
      [ 'easter' => 49,      'name' => 'Whit Sunday',             'local' => 'Pfingstsonntag', 'in' => [ 'bb' ] ],
      [ 'easter' => 50,      'name' => 'Whit Monday',             'local' => 'Pfingstmontag' ],
      [ 'easter' => 60,      'name' => 'Corpus Christi',          'local' => 'Fronleichnam', 'in' => [ 'bw', 'by', 'he', 'nw', 'rp', 'sl' ] ],
      [ 'date'   => '06-17', 'name' => 'Day of German Unity',     'local' => 'Tag der deutschen Einheit', 'from' => 1954, 'to' => 1990 ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day',          'local' => 'Mariä Himmelfahrt', 'in' => [ 'sl' ] ],
      [ 'date'   => '09-20', 'name' => "World Children's Day",    'local' => 'Weltkindertag', 'in' => [ 'th' ], 'from' => 2019 ],
      [ 'date'   => '10-03', 'name' => 'Day of German Unity',     'local' => 'Tag der Deutschen Einheit', 'from' => 1990 ],
      [ 'date'   => '10-31', 'name' => 'Reformation Day',         'local' => 'Reformationstag', 'from' => 2017, 'to' => 2017, 'not' => [ 'bb', 'mv', 'sn', 'st', 'th' ] ],
      [ 'date'   => '10-31', 'name' => 'Reformation Day',         'local' => 'Reformationstag', 'in' => [ 'bb', 'mv', 'sn', 'st', 'th' ], 'from' => 1990 ],
      [ 'date'   => '10-31', 'name' => 'Reformation Day',         'local' => 'Reformationstag', 'in' => [ 'hb', 'hh', 'ni', 'sh' ], 'from' => 2018 ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",         'local' => 'Allerheiligen', 'in' => [ 'bw', 'by', 'nw', 'rp', 'sl' ] ],
      [ 'onOrBefore' => [ 'wed', '11-22' ], 'name' => 'Day of Repentance and Prayer', 'local' => 'Buß- und Bettag', 'to' => 1994, 'not' => [ 'sn' ] ],
      [ 'onOrBefore' => [ 'wed', '11-22' ], 'name' => 'Day of Repentance and Prayer', 'local' => 'Buß- und Bettag', 'in' => [ 'sn' ] ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',           'local' => 'Erster Weihnachtstag' ],
      [ 'date'   => '12-26', 'name' => 'Second Day of Christmas', 'local' => 'Zweiter Weihnachtstag' ]
    ]
  ];

?>

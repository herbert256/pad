<?php

  // pl - Poland: the public holidays of the Act on Days Free from Work (ustawa o dniach wolnych
  // od pracy), exact since 2011, when Epiphany came back, Easter and Pentecost Sunday among
  // them; Christmas Eve since 2025, and the one-off 12 November 2018, a hundred years of
  // independence. A holiday on a Saturday gives the employee another day off of the
  // employer's choosing, no set date: no substitute is given.

  return [
    'name'  => 'Poland',
    'langs' => [ 'pl' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",          'local' => 'Nowy Rok' ],
      [ 'date'   => '01-06', 'name' => 'Epiphany',                'local' => 'Święto Trzech Króli', 'from' => 2011 ],
      [ 'easter' => 0,       'name' => 'Easter Sunday',           'local' => 'Wielkanoc' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',           'local' => 'Poniedziałek Wielkanocny' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',              'local' => 'Święto Pracy' ],
      [ 'date'   => '05-03', 'name' => 'Constitution Day',        'local' => 'Święto Narodowe Trzeciego Maja' ],
      [ 'easter' => 49,      'name' => 'Pentecost Sunday',        'local' => 'Zielone Świątki' ],
      [ 'easter' => 60,      'name' => 'Corpus Christi',          'local' => 'Boże Ciało' ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day',          'local' => 'Wniebowzięcie Najświętszej Maryi Panny' ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",         'local' => 'Wszystkich Świętych' ],
      [ 'date'   => '11-11', 'name' => 'Independence Day',        'local' => 'Narodowe Święto Niepodległości' ],
      [ 'date'   => '11-12', 'name' => 'Centenary of Independence', 'local' => 'Święto Narodowe z okazji 100. rocznicy odzyskania niepodległości', 'from' => 2018, 'to' => 2018 ],
      [ 'date'   => '12-24', 'name' => 'Christmas Eve',           'local' => 'Wigilia Bożego Narodzenia', 'from' => 2025 ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',           'local' => 'Boże Narodzenie (pierwszy dzień)' ],
      [ 'date'   => '12-26', 'name' => 'Second Day of Christmas', 'local' => 'Boże Narodzenie (drugi dzień)' ]
    ]
  ];

?>

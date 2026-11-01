<?php

  // nl - the Netherlands: the national holidays of the Algemene termijnenwet, with Easter and
  // Whit Sunday, which fall on a Sunday anyway. Koningsdag is 27 April, the 26th when the 27th
  // is a Sunday (2014, 2025); before it Koninginnedag was 30 April, the 29th when that was a
  // Sunday (1980-2013). Bevrijdingsdag, 5 May, is a national holiday every year since 1990,
  // but a day off for the civil service and by most agreements only every fifth year (2025,
  // 2030): a public holiday in those years, an observance in the others. Good Friday is no
  // day off by law - an observance. No regions: the Caribbean Netherlands are left out.

  return [
    'name'  => 'Netherlands',
    'langs' => [ 'nl' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",          'local' => 'Nieuwjaarsdag' ],
      [ 'easter' => -2,      'name' => 'Good Friday',             'local' => 'Goede Vrijdag', 'type' => 'observance' ],
      [ 'easter' => 0,       'name' => 'Easter Sunday',           'local' => 'Eerste Paasdag' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',           'local' => 'Tweede Paasdag' ],
      [ 'date'   => '04-30', 'name' => "Queen's Day",             'local' => 'Koninginnedag', 'from' => 1980, 'to' => 2013, 'sunday' => -1 ],
      [ 'date'   => '04-27', 'name' => "King's Day",              'local' => 'Koningsdag',    'from' => 2014, 'sunday' => -1 ],
      [ 'date'   => '05-05', 'name' => 'Liberation Day',          'local' => 'Bevrijdingsdag', 'from' => 1990, 'publicEvery' => 5 ],
      [ 'easter' => 39,      'name' => 'Ascension Day',           'local' => 'Hemelvaartsdag' ],
      [ 'easter' => 49,      'name' => 'Whit Sunday',             'local' => 'Eerste Pinksterdag' ],
      [ 'easter' => 50,      'name' => 'Whit Monday',             'local' => 'Tweede Pinksterdag' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',           'local' => 'Eerste Kerstdag' ],
      [ 'date'   => '12-26', 'name' => 'Second Day of Christmas', 'local' => 'Tweede Kerstdag' ]
    ]
  ];

?>

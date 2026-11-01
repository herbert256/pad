<?php

  // no - Norway: the public holidays of the Public Holidays Act (helligdager) and the two
  // national days of 1947 - 1 May and Constitution Day, 17 May - Easter and Whit Sunday among
  // them; names in Bokmål. Svalbard and Jan Mayen keep the same days.

  return [
    'name'  => 'Norway',
    'langs' => [ 'nb' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",     'local' => 'Første nyttårsdag' ],
      [ 'easter' => -3,      'name' => 'Maundy Thursday',    'local' => 'Skjærtorsdag' ],
      [ 'easter' => -2,      'name' => 'Good Friday',        'local' => 'Langfredag' ],
      [ 'easter' => 0,       'name' => 'Easter Sunday',      'local' => 'Første påskedag' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',      'local' => 'Andre påskedag' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',         'local' => 'Arbeidernes dag' ],
      [ 'date'   => '05-17', 'name' => 'Constitution Day',   'local' => 'Grunnlovsdag' ],
      [ 'easter' => 39,      'name' => 'Ascension Day',      'local' => 'Kristi himmelfartsdag' ],
      [ 'easter' => 49,      'name' => 'Whit Sunday',        'local' => 'Første pinsedag' ],
      [ 'easter' => 50,      'name' => 'Whit Monday',        'local' => 'Andre pinsedag' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',      'local' => 'Første juledag' ],
      [ 'date'   => '12-26', 'name' => 'Second Day of Christmas', 'local' => 'Andre juledag' ]
    ]
  ];

?>

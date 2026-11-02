<?php

  // dk - Denmark: the public holidays of the Danish church year (helligdage), Easter and Whit
  // Sunday among them; Great Prayer Day, the fourth Friday after Easter, until 2023 - it was
  // abolished from 2024. Constitution Day, 5 June, is a half day off by many agreements and
  // no public holiday: an observance. Christmas Eve and New Year's Eve are days off by custom,
  // not by law, and left out. Greenland and the Faroe Islands are left out.

  return [
    'name'  => 'Denmark',
    'langs' => [ 'da' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",    'local' => 'Nytårsdag' ],
      [ 'easter' => -3,      'name' => 'Maundy Thursday',   'local' => 'Skærtorsdag' ],
      [ 'easter' => -2,      'name' => 'Good Friday',       'local' => 'Langfredag' ],
      [ 'easter' => 0,       'name' => 'Easter Sunday',     'local' => 'Påskedag' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',     'local' => '2. påskedag' ],
      [ 'easter' => 26,      'name' => 'Great Prayer Day',  'local' => 'Store bededag', 'to' => 2023 ],
      [ 'easter' => 39,      'name' => 'Ascension Day',     'local' => 'Kristi himmelfartsdag' ],
      [ 'easter' => 49,      'name' => 'Whit Sunday',       'local' => 'Pinsedag' ],
      [ 'easter' => 50,      'name' => 'Whit Monday',       'local' => '2. pinsedag' ],
      [ 'date'   => '06-05', 'name' => 'Constitution Day',  'local' => 'Grundlovsdag', 'type' => 'observance' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',     'local' => 'Juledag' ],
      [ 'date'   => '12-26', 'name' => 'Second Day of Christmas', 'local' => '2. juledag' ]
    ]
  ];

?>

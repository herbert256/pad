<?php

  // gb - the United Kingdom: the bank holidays of England and Wales, and with region= those of
  // a nation by its ISO 3166-2 code - eng and wls the same, sct Scotland (2 January, the first
  // Monday of August, St Andrew's Day; no Easter Monday), nir Northern Ireland (St Patrick's
  // Day, the Battle of the Boyne). A holiday on a weekend gets a substitute day, the first
  // weekday after it that is no bank holiday yet: Christmas on a Saturday is made up for on
  // Monday the 27th, Boxing Day on the Sunday on Tuesday the 28th. Exact since 2002, with the
  // one-off bank holidays since - jubilees, a wedding, a funeral, a coronation - and the moved
  // ones: the early May bank holiday on VE Day in 1995 and 2020, the spring bank holiday in
  // 2002, 2012 and 2022.

  return [
    'name'       => 'United Kingdom',
    'langs'      => [ 'en' ],
    'regions'    => [ 'eng' => 'England', 'sct' => 'Scotland', 'wls' => 'Wales', 'nir' => 'Northern Ireland' ],
    'substitute' => 'next',
    'suffix'     => ' (substitute day)',
    'days'       => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",                      'substitute' => TRUE ],
      [ 'date'   => '01-02', 'name' => '2nd January',                         'substitute' => TRUE, 'in' => [ 'sct' ] ],
      [ 'date'   => '03-17', 'name' => "St Patrick's Day",                    'substitute' => TRUE, 'in' => [ 'nir' ] ],
      [ 'easter' => -2,      'name' => 'Good Friday' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',                       'not' => [ 'sct' ] ],
      [ 'date'   => '04-29', 'name' => 'Wedding of Prince William and Catherine Middleton', 'from' => 2011, 'to' => 2011 ],
      [ 'nth'    => [ 1, 'mon', 5 ], 'name' => 'Early May bank holiday',      'from' => 1978, 'moved' => [ 1995 => '05-08', 2020 => '05-08' ] ],
      [ 'date'   => '05-08', 'name' => 'Coronation of King Charles III',      'from' => 2023, 'to' => 2023 ],
      [ 'nth'    => [ -1, 'mon', 5 ], 'name' => 'Spring bank holiday',        'from' => 1971, 'moved' => [ 2002 => '06-04', 2012 => '06-04', 2022 => '06-02' ] ],
      [ 'date'   => '06-03', 'name' => 'Golden Jubilee of Queen Elizabeth II',   'from' => 2002, 'to' => 2002 ],
      [ 'date'   => '06-05', 'name' => 'Diamond Jubilee of Queen Elizabeth II',  'from' => 2012, 'to' => 2012 ],
      [ 'date'   => '06-03', 'name' => 'Platinum Jubilee of Queen Elizabeth II', 'from' => 2022, 'to' => 2022 ],
      [ 'date'   => '07-12', 'name' => "Battle of the Boyne (Orangemen's Day)", 'substitute' => TRUE, 'in' => [ 'nir' ] ],
      [ 'nth'    => [ 1, 'mon', 8 ], 'name' => 'Summer bank holiday',         'in' => [ 'sct' ] ],
      [ 'nth'    => [ -1, 'mon', 8 ], 'name' => 'Summer bank holiday',        'from' => 1971, 'not' => [ 'sct' ] ],
      [ 'date'   => '09-19', 'name' => 'State Funeral of Queen Elizabeth II', 'from' => 2022, 'to' => 2022 ],
      [ 'date'   => '11-30', 'name' => "St Andrew's Day",                     'substitute' => TRUE, 'in' => [ 'sct' ], 'from' => 2007 ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',                       'substitute' => TRUE ],
      [ 'date'   => '12-26', 'name' => 'Boxing Day',                          'substitute' => TRUE ],
      [ 'date'   => '12-31', 'name' => 'Millennium Day',                      'from' => 1999, 'to' => 1999 ]
    ]
  ];

?>

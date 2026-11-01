<?php

  // ie - Ireland: the public holidays of the Organisation of Working Time Act, exact since
  // 1994, when the May bank holiday came; St Brigid's Day since 2023 - the first Monday of
  // February, or 1 February when that is a Friday - and the one-off Day of Remembrance and
  // Recognition of 18 March 2022. Good Friday is no public holiday. A holiday on a weekend
  // gives the employee a day off in lieu, not a set date: no substitute day is given.

  return [
    'name'  => 'Ireland',
    'langs' => [ 'ga' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",       'local' => 'Lá Caille' ],
      [ 'calc'   => function ( $year ) {
                      $first = padHolidaysDay ( $year, 2, 1 );
                      $wd    = padHolidaysWeekday ( $first );
                      return padHolidaysDate ( $wd == 5 ? $first : $first + ( 8 - $wd ) % 7 );
                    },
                     'name' => "St Brigid's Day",       'local' => 'Lá Fhéile Bríde', 'from' => 2023 ],
      [ 'date'   => '03-17', 'name' => "St Patrick's Day",     'local' => 'Lá Fhéile Pádraig' ],
      [ 'date'   => '03-18', 'name' => 'Day of Remembrance and Recognition', 'local' => 'Lá Cuimhneacháin agus Aitheantais', 'from' => 2022, 'to' => 2022 ],
      [ 'easter' => 1,       'name' => 'Easter Monday',        'local' => 'Luan Cásca' ],
      [ 'nth'    => [ 1, 'mon', 5 ],  'name' => 'May Bank Holiday',     'local' => 'Lá Saoire i mí na Bealtaine' ],
      [ 'nth'    => [ 1, 'mon', 6 ],  'name' => 'June Bank Holiday',    'local' => 'Lá Saoire i mí an Mheithimh' ],
      [ 'nth'    => [ 1, 'mon', 8 ],  'name' => 'August Bank Holiday',  'local' => 'Lá Saoire i mí Lúnasa' ],
      [ 'nth'    => [ -1, 'mon', 10 ], 'name' => 'October Bank Holiday', 'local' => 'Lá Saoire i mí Dheireadh Fómhair' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',        'local' => 'Lá Nollag' ],
      [ 'date'   => '12-26', 'name' => "St Stephen's Day",     'local' => 'Lá Fhéile Stiofáin' ]
    ]
  ];

?>

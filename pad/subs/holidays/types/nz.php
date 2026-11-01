<?php

  // nz - New Zealand: the public holidays of the Holidays Act 2003, s 44, and with region= the
  // anniversary day of a province, by the ISO 3166-2 code of the region of its name - nz-auk
  // Auckland, nz-wgn Wellington, nz-can Canterbury (Christchurch Show Day, the second Friday
  // after the first Tuesday of November), nz-wtc Westland, nz-cit the Chatham Islands ...
  // A province is one of the nineteenth century, not today's region: Northland, Waikato and
  // the others without a province of their own have no anniversary day here, nor does South
  // Canterbury, which keeps Dominion Day. Christmas, Boxing Day, New Year's Day and 2 January
  // on a weekend are observed on the next weekday that is no holiday yet - Christmas on a
  // Saturday on Monday the 27th, Boxing Day on the Sunday on Tuesday the 28th; Waitangi Day
  // and Anzac Day on the following Monday since 2014 (the Holidays (Full Recognition of
  // Waitangi Day and ANZAC Day) Amendment Act 2013) - in 2038, Anzac Day a Sunday and Easter
  // Sunday, on Easter Monday. Matariki is the date of Schedule 1 of Te Kāhui o Matariki Public
  // Holiday Act 2022, a Friday from 2022 to 2052; later dates are added by Order in Council,
  // so the file knows the years 2010 to 2052. Checked against Employment New Zealand's
  // "Public holidays and anniversary dates" for 2010 to 2027, every national and anniversary
  // date, actual and observed, and the Matariki schedule as Te Papa lists it. The Otago day
  // is the Monday nearest 23 March that Employment New Zealand publishes, though it says there
  // is no single day of local observance; in 2035 that Monday is Easter Monday.

  $matariki = [ 2022 => '06-24', 2023 => '07-14', 2024 => '06-28', 2025 => '06-20', 2026 => '07-10', 2027 => '06-25',
                2028 => '07-14', 2029 => '07-06', 2030 => '06-21', 2031 => '07-11', 2032 => '07-02', 2033 => '06-24',
                2034 => '07-07', 2035 => '06-29', 2036 => '07-18', 2037 => '07-10', 2038 => '06-25', 2039 => '07-15',
                2040 => '07-06', 2041 => '07-19', 2042 => '07-11', 2043 => '07-03', 2044 => '06-24', 2045 => '07-07',
                2046 => '06-29', 2047 => '07-19', 2048 => '07-03', 2049 => '06-25', 2050 => '07-15', 2051 => '06-30',
                2052 => '06-21' ];

  // Labour Day, the fourth Monday of October, from which Hawke's Bay and Marlborough count.

  $labour = fn ( $year ) => padHolidaysRule ( [ 'nth' => [ 4, 'mon', 10 ] ], $year );

  return [
    'name'       => 'New Zealand',
    'langs'      => [ 'en' ],
    'regions'    => [ 'auk' => 'Auckland', 'can' => 'Canterbury', 'cit' => 'Chatham Islands', 'hkb' => "Hawke's Bay",
                      'mbh' => 'Marlborough', 'nsn' => 'Nelson', 'ota' => 'Otago', 'stl' => 'Southland',
                      'tki' => 'Taranaki', 'wgn' => 'Wellington', 'wtc' => 'Westland' ],
    'substitute' => 'next',
    'suffix'     => ' (observed)',
    'years'      => [ 2010, 2052 ],
    'days'       => [
      [ 'date'      => '01-01',          'name' => "New Year's Day",                  'substitute' => TRUE ],
      [ 'date'      => '01-02',          'name' => "Day after New Year's Day",        'substitute' => TRUE ],
      [ 'onOrAfter' => [ 'mon', '01-14' ], 'name' => 'Southland Anniversary Day',     'in' => [ 'stl' ], 'to' => 2011 ],
      [ 'onOrAfter' => [ 'mon', '01-19' ], 'name' => 'Wellington Anniversary Day',    'in' => [ 'wgn' ] ],
      [ 'onOrAfter' => [ 'mon', '01-26' ], 'name' => 'Auckland Anniversary Day',      'in' => [ 'auk' ] ],
      [ 'onOrAfter' => [ 'mon', '01-29' ], 'name' => 'Nelson Anniversary Day',        'in' => [ 'nsn' ] ],
      [ 'date'      => '02-06',          'name' => 'Waitangi Day',                    'to' => 2013 ],
      [ 'date'      => '02-06',          'name' => 'Waitangi Day',                    'from' => 2014, 'substitute' => 'monday' ],
      [ 'nth'       => [ 2, 'mon', 3 ],  'name' => 'Taranaki Anniversary Day',        'in' => [ 'tki' ] ],
      [ 'onOrAfter' => [ 'mon', '03-20' ], 'name' => 'Otago Anniversary Day',         'in' => [ 'ota' ] ],
      [ 'easter'    => -2,               'name' => 'Good Friday' ],
      [ 'easter'    => 1,                'name' => 'Easter Monday' ],
      [ 'easter'    => 2,                'name' => 'Southland Anniversary Day',       'in' => [ 'stl' ], 'from' => 2012 ],
      [ 'date'      => '04-25',          'name' => 'Anzac Day',                       'to' => 2013 ],
      [ 'date'      => '04-25',          'name' => 'Anzac Day',                       'from' => 2014, 'substitute' => 'monday' ],
      [ 'nth'       => [ 1, 'mon', 6 ],  'name' => "Queen's Birthday",                'to' => 2022 ],
      [ 'nth'       => [ 1, 'mon', 6 ],  'name' => "King's Birthday",                 'from' => 2023 ],
      [ 'calc'      => fn ( $year ) => isset ( $matariki [$year] ) ? "$year-{$matariki [$year]}" : NULL,
                                         'name' => 'Matariki',                        'from' => 2022, 'to' => 2052 ],
      [ 'date'      => '09-26',          'name' => 'Queen Elizabeth II Memorial Day', 'from' => 2022, 'to' => 2022 ],
      [ 'calc'      => fn ( $year ) => padHolidaysDate ( $labour ( $year ) - 3 ),
                                         'name' => "Hawke's Bay Anniversary Day",     'in' => [ 'hkb' ] ],
      [ 'nth'       => [ 4, 'mon', 10 ], 'name' => 'Labour Day' ],
      [ 'calc'      => fn ( $year ) => padHolidaysDate ( $labour ( $year ) + 7 ),
                                         'name' => 'Marlborough Anniversary Day',     'in' => [ 'mbh' ] ],
      [ 'calc'      => fn ( $year ) => padHolidaysDate ( padHolidaysRule ( [ 'nth' => [ 1, 'tue', 11 ] ], $year ) + 10 ),
                                         'name' => 'Canterbury Anniversary Day (Show Day)', 'in' => [ 'can' ] ],
      [ 'onOrAfter' => [ 'mon', '11-27' ], 'name' => 'Chatham Islands Anniversary Day', 'in' => [ 'cit' ] ],
      [ 'onOrAfter' => [ 'mon', '11-28' ], 'name' => 'Westland Anniversary Day',      'in' => [ 'wtc' ] ],
      [ 'date'      => '12-25',          'name' => 'Christmas Day',                   'substitute' => TRUE ],
      [ 'date'      => '12-26',          'name' => 'Boxing Day',                      'substitute' => TRUE ]
    ]
  ];

?>

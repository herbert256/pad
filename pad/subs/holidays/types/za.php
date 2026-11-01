<?php

  // za - South Africa: the twelve public holidays of the Public Holidays Act 36 of 1994. A
  // holiday on a Sunday makes the Monday after it a public holiday (section 2(1)) - one on a
  // Saturday gets nothing - and when that Monday is a holiday already, as the Day of Goodwill
  // after Christmas on a Sunday, the act gives no day more: 27 December was a public holiday
  // in 2016 and 2022 because the President declared it (section 2A), as he declares the day
  // of every general and local election - 7 May 2014, 3 August 2016, 8 May 2019, 1 November
  // 2021, 29 May 2024 and 4 November 2026. A day declared later is added to this file when it
  // is gazetted. Names in English, one of the eleven official languages. The file knows the
  // years since 2014. Checked against the act's section 2 as the Presidency quotes it, the
  // declarations of 2014 (Government Gazette of 25 February 2014), 2016, 2019, 2021, 2022,
  // 2024 and 2026 as the Presidency and SAnews published them, and the dates of 2016, 2022,
  // 2024, 2025 and 2026 (National Women's Day on Sunday 9 August 2026, Monday 10 August off).

  return [
    'name'       => 'South Africa',
    'langs'      => [ 'en' ],
    'substitute' => 'sunday',
    'suffix'     => ' (observed)',
    'years'      => [ 2014, padHolidaysLast ],
    'days'       => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",          'substitute' => TRUE ],
      [ 'date'   => '03-21', 'name' => 'Human Rights Day',        'substitute' => TRUE ],
      [ 'easter' => -2,      'name' => 'Good Friday' ],
      [ 'easter' => 1,       'name' => 'Family Day' ],
      [ 'date'   => '04-27', 'name' => 'Freedom Day',             'substitute' => TRUE ],
      [ 'date'   => '05-01', 'name' => "Workers' Day",            'substitute' => TRUE ],
      [ 'date'   => '05-07', 'name' => 'Election Day',            'from' => 2014, 'to' => 2014 ],
      [ 'date'   => '05-08', 'name' => 'Election Day',            'from' => 2019, 'to' => 2019 ],
      [ 'date'   => '05-29', 'name' => 'Election Day',            'from' => 2024, 'to' => 2024 ],
      [ 'date'   => '06-16', 'name' => 'Youth Day',               'substitute' => TRUE ],
      [ 'date'   => '08-03', 'name' => 'Election Day',            'from' => 2016, 'to' => 2016 ],
      [ 'date'   => '08-09', 'name' => "National Women's Day",    'substitute' => TRUE ],
      [ 'date'   => '09-24', 'name' => 'Heritage Day',            'substitute' => TRUE ],
      [ 'date'   => '11-01', 'name' => 'Election Day',            'from' => 2021, 'to' => 2021 ],
      [ 'date'   => '11-04', 'name' => 'Election Day',            'from' => 2026, 'to' => 2026 ],
      [ 'date'   => '12-16', 'name' => 'Day of Reconciliation',   'substitute' => TRUE ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',           'substitute' => TRUE ],
      [ 'date'   => '12-26', 'name' => 'Day of Goodwill',         'substitute' => TRUE ],
      [ 'date'   => '12-27', 'name' => 'Public holiday in lieu of Christmas Day', 'from' => 2016, 'to' => 2016 ],
      [ 'date'   => '12-27', 'name' => 'Public holiday in lieu of Christmas Day', 'from' => 2022, 'to' => 2022 ]
    ]
  ];

?>

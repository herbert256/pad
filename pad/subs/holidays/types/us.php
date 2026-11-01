<?php

  // us - the United States: the eleven federal holidays of 5 U.S.C. 6103, exact since 1986,
  // when Martin Luther King Jr. Day came; Juneteenth since 2021. A holiday on a Saturday is
  // observed on the Friday before, on a Sunday on the Monday after - the observed day is a
  // row of its own, and may fall in the year before: New Year's Day 2022, a Saturday, was
  // observed on Friday 31 December 2021. Inauguration Day, a holiday around Washington only,
  // and the states' own holidays are left out.

  return [
    'name'       => 'United States',
    'langs'      => [ 'en' ],
    'substitute' => 'nearest',
    'suffix'     => ' (observed)',
    'days'       => [
      [ 'date'   => '01-01',             'name' => "New Year's Day",                    'substitute' => TRUE ],
      [ 'nth'    => [ 3, 'mon', 1 ],     'name' => 'Martin Luther King Jr. Day',        'from' => 1986 ],
      [ 'nth'    => [ 3, 'mon', 2 ],     'name' => "Washington's Birthday",             'from' => 1971 ],
      [ 'nth'    => [ -1, 'mon', 5 ],    'name' => 'Memorial Day',                      'from' => 1971 ],
      [ 'date'   => '06-19',             'name' => 'Juneteenth National Independence Day', 'substitute' => TRUE, 'from' => 2021 ],
      [ 'date'   => '07-04',             'name' => 'Independence Day',                  'substitute' => TRUE ],
      [ 'nth'    => [ 1, 'mon', 9 ],     'name' => 'Labor Day' ],
      [ 'nth'    => [ 2, 'mon', 10 ],    'name' => 'Columbus Day',                      'from' => 1971 ],
      [ 'date'   => '11-11',             'name' => 'Veterans Day',                      'substitute' => TRUE, 'from' => 1978 ],
      [ 'nth'    => [ 4, 'thu', 11 ],    'name' => 'Thanksgiving Day' ],
      [ 'date'   => '12-25',             'name' => 'Christmas Day',                     'substitute' => TRUE ]
    ]
  ];

?>

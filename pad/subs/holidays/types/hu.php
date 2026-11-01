<?php

  // hu - Hungary: the rest days of the Labour Code (2012. évi I. törvény), § 102 (1), with
  // Easter and Whit Sunday, which § 102 (4) treats as rest days, and Good Friday since 2017
  // (the amendment adopted on 7 March 2017, first held on 14 April). The yearly decree of
  // the minister that moves working days around the holidays - a bridge day off, a Saturday
  // worked for it - is left out: those are rearranged working days, no holidays. The file
  // knows the years since 2013, the first whole year of the code (in force from 1 July
  // 2012). Checked against § 102 of the code as the Nemzeti Jogszabálytár publishes it and
  // the dates of 2016, 2017, 2024, 2025 and 2026.

  return [
    'name'  => 'Hungary',
    'langs' => [ 'hu' ],
    'years' => [ 2013, padHolidaysLast ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",              'local' => 'Újév' ],
      [ 'date'   => '03-15', 'name' => 'Revolution Day of 1848',      'local' => 'Az 1848-as forradalom ünnepe' ],
      [ 'easter' => -2,      'name' => 'Good Friday',                 'local' => 'Nagypéntek', 'from' => 2017 ],
      [ 'easter' => 0,       'name' => 'Easter Sunday',               'local' => 'Húsvétvasárnap' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',               'local' => 'Húsvéthétfő' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',                  'local' => 'A munka ünnepe' ],
      [ 'easter' => 49,      'name' => 'Whit Sunday',                 'local' => 'Pünkösdvasárnap' ],
      [ 'easter' => 50,      'name' => 'Whit Monday',                 'local' => 'Pünkösdhétfő' ],
      [ 'date'   => '08-20', 'name' => "St Stephen's Day",            'local' => 'Az államalapítás ünnepe' ],
      [ 'date'   => '10-23', 'name' => 'Revolution Day of 1956',      'local' => 'Az 1956-os forradalom ünnepe' ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",             'local' => 'Mindenszentek' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',               'local' => 'Karácsony' ],
      [ 'date'   => '12-26', 'name' => 'Second Day of Christmas',     'local' => 'Karácsony másnapja' ]
    ]
  ];

?>

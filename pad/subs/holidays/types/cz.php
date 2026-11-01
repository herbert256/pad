<?php

  // cz - Czechia: the state holidays and other holidays of Act 245/2000 Sb., all of them days
  // of rest (§ 3) - 1 January twice, as the Restoration Day of the Independent Czech State
  // and as New Year's Day - with Good Friday since 2016 (Act 359/2015 Sb., in force from
  // 21 December 2015). The file knows the years since 2001, the first whole year of the act
  // (in force from 9 August 2000); the significant days of § 4 are working days and left
  // out. Checked against the act's current text and its amendment of 2015 on
  // zakonyprolidi.cz, and the dates of 2001, 2015, 2016, 2025 and 2026.

  return [
    'name'  => 'Czechia',
    'langs' => [ 'cs' ],
    'years' => [ 2001, padHolidaysLast ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => 'Restoration Day of the Independent Czech State', 'local' => 'Den obnovy samostatného českého státu' ],
      [ 'date'   => '01-01', 'name' => "New Year's Day",                         'local' => 'Nový rok' ],
      [ 'easter' => -2,      'name' => 'Good Friday',                            'local' => 'Velký pátek', 'from' => 2016 ],
      [ 'easter' => 1,       'name' => 'Easter Monday',                          'local' => 'Velikonoční pondělí' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',                             'local' => 'Svátek práce' ],
      [ 'date'   => '05-08', 'name' => 'Victory Day',                            'local' => 'Den vítězství' ],
      [ 'date'   => '07-05', 'name' => 'Saints Cyril and Methodius Day',         'local' => 'Den slovanských věrozvěstů Cyrila a Metoděje' ],
      [ 'date'   => '07-06', 'name' => 'Jan Hus Day',                            'local' => 'Den upálení mistra Jana Husa' ],
      [ 'date'   => '09-28', 'name' => 'Czech Statehood Day',                    'local' => 'Den české státnosti' ],
      [ 'date'   => '10-28', 'name' => 'Independent Czechoslovak State Day',     'local' => 'Den vzniku samostatného československého státu' ],
      [ 'date'   => '11-17', 'name' => 'Struggle for Freedom and Democracy Day', 'local' => 'Den boje za svobodu a demokracii a Mezinárodní den studentstva' ],
      [ 'date'   => '12-24', 'name' => 'Christmas Eve',                          'local' => 'Štědrý den' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',                          'local' => '1. svátek vánoční' ],
      [ 'date'   => '12-26', 'name' => "St Stephen's Day",                       'local' => '2. svátek vánoční' ]
    ]
  ];

?>

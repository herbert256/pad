<?php

  // sk - Slovakia: the state holidays and days of rest of Act 241/1993 Z. z., which the
  // consolidation of the public finances has been cutting: Constitution Day, 1 September,
  // is no day of rest since 2024 (Act 530/2023), the Struggle for Freedom and Democracy Day,
  // 17 November, since 1 November 2025 (Act 261/2025), and in 2026 alone neither are Victory
  // over Fascism Day, 8 May, and Our Lady of Seven Sorrows, 15 September (its § 4b) - a state
  // holiday that is no day of rest is an observance here, as is 28 October, a state holiday
  // since 2021 that has never been a day of rest (Act 326/2020). In 2018 the centenary of the Declaration of
  // the Slovak Nation, 30 October, was a state holiday (Act 281/2018). The file knows the
  // years since 2002, the first whole year with 17 November a state holiday (Act 442/2001).
  // Checked against the act's versions of 2016, 2021, 2023, 2024, 1 November 2025 and 29
  // October 2026 and the amending acts on slov-lex.sk, the Ministry of Justice's register.

  return [
    'name'  => 'Slovakia',
    'langs' => [ 'sk' ],
    'years' => [ 2002, padHolidaysLast ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => 'Day of the Establishment of the Slovak Republic', 'local' => 'Deň vzniku Slovenskej republiky' ],
      [ 'date'   => '01-06', 'name' => 'Epiphany',                               'local' => 'Zjavenie Pána (Traja králi)' ],
      [ 'easter' => -2,      'name' => 'Good Friday',                            'local' => 'Veľký piatok' ],
      [ 'easter' => 1,       'name' => 'Easter Monday',                          'local' => 'Veľkonočný pondelok' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',                             'local' => 'Sviatok práce' ],
      [ 'date'   => '05-08', 'name' => 'Day of Victory over Fascism',            'local' => 'Deň víťazstva nad fašizmom', 'to' => 2025 ],
      [ 'date'   => '05-08', 'name' => 'Day of Victory over Fascism',            'local' => 'Deň víťazstva nad fašizmom', 'from' => 2026, 'to' => 2026, 'type' => 'observance' ],
      [ 'date'   => '05-08', 'name' => 'Day of Victory over Fascism',            'local' => 'Deň víťazstva nad fašizmom', 'from' => 2027 ],
      [ 'date'   => '07-05', 'name' => 'Saints Cyril and Methodius Day',         'local' => 'Sviatok svätého Cyrila a svätého Metoda' ],
      [ 'date'   => '08-29', 'name' => 'Slovak National Uprising Anniversary',   'local' => 'Výročie Slovenského národného povstania' ],
      [ 'date'   => '09-01', 'name' => 'Constitution Day',                       'local' => 'Deň Ústavy Slovenskej republiky', 'to' => 2023 ],
      [ 'date'   => '09-01', 'name' => 'Constitution Day',                       'local' => 'Deň Ústavy Slovenskej republiky', 'from' => 2024, 'type' => 'observance' ],
      [ 'date'   => '09-15', 'name' => 'Our Lady of Seven Sorrows',              'local' => 'Sedembolestná Panna Mária', 'to' => 2025 ],
      [ 'date'   => '09-15', 'name' => 'Our Lady of Seven Sorrows',              'local' => 'Sedembolestná Panna Mária', 'from' => 2026, 'to' => 2026, 'type' => 'observance' ],
      [ 'date'   => '09-15', 'name' => 'Our Lady of Seven Sorrows',              'local' => 'Sedembolestná Panna Mária', 'from' => 2027 ],
      [ 'date'   => '10-28', 'name' => 'Day of the Establishment of the Independent Czecho-Slovak State', 'local' => 'Deň vzniku samostatného česko-slovenského štátu',
                                                                                 'from' => 2021, 'type' => 'observance' ],
      [ 'date'   => '10-30', 'name' => 'Anniversary of the Declaration of the Slovak Nation', 'local' => 'Výročie Deklarácie slovenského národa', 'from' => 2018, 'to' => 2018 ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",                        'local' => 'Sviatok Všetkých svätých' ],
      [ 'date'   => '11-17', 'name' => 'Struggle for Freedom and Democracy Day', 'local' => 'Deň boja za slobodu a demokraciu', 'to' => 2024 ],
      [ 'date'   => '11-17', 'name' => 'Struggle for Freedom and Democracy Day', 'local' => 'Deň boja za slobodu a demokraciu', 'from' => 2025, 'type' => 'observance' ],
      [ 'date'   => '12-24', 'name' => 'Christmas Eve',                          'local' => 'Štedrý deň' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',                          'local' => 'Prvý sviatok vianočný' ],
      [ 'date'   => '12-26', 'name' => "St Stephen's Day",                       'local' => 'Druhý sviatok vianočný' ]
    ]
  ];

?>

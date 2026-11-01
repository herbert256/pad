<?php

  // br - Brazil: the national holidays of federal law - Lei 662/1949 with Lei 10.607/2002
  // (1 January, 21 April, 1 May, 7 September, 2 and 15 November, 25 December), Lei 6.802/1980
  // (Our Lady of Aparecida, 12 October) and, since 2024, Lei 14.759/2023 (Zumbi and Black
  // Consciousness Day, 20 November) - with Good Friday, which the federal government's yearly
  // ordinance of holidays lists as a national one. Carnival Monday and Tuesday and Corpus
  // Christi are optional days of that ordinance (pontos facultativos), no days off by law -
  // observances; Ash Wednesday's morning, the civil servants' day and the afternoons of
  // 24 and 31 December are left out, as are the holidays of the states and towns. The file
  // knows the years since 2003. Checked against the ordinances of the Ministry of Management
  // (Portaria MGI 11.460/2025 for 2026: Carnival 16 and 17 February, Good Friday 3 April,
  // Corpus Christi 4 June, 20 November a national holiday) and the laws they name, and the
  // dates of 2003, 2023, 2024, 2025 and 2026.

  return [
    'name'  => 'Brazil',
    'langs' => [ 'pt' ],
    'years' => [ 2003, padHolidaysLast ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",                    'local' => 'Confraternização Universal' ],
      [ 'easter' => -48,     'name' => 'Carnival Monday',                   'local' => 'Carnaval', 'type' => 'observance' ],
      [ 'easter' => -47,     'name' => 'Carnival Tuesday',                  'local' => 'Carnaval', 'type' => 'observance' ],
      [ 'easter' => -2,      'name' => 'Good Friday',                       'local' => 'Paixão de Cristo' ],
      [ 'date'   => '04-21', 'name' => 'Tiradentes',                        'local' => 'Tiradentes' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',                        'local' => 'Dia Mundial do Trabalho' ],
      [ 'easter' => 60,      'name' => 'Corpus Christi',                    'local' => 'Corpus Christi', 'type' => 'observance' ],
      [ 'date'   => '09-07', 'name' => 'Independence Day',                  'local' => 'Independência do Brasil' ],
      [ 'date'   => '10-12', 'name' => 'Our Lady of Aparecida',             'local' => 'Nossa Senhora Aparecida' ],
      [ 'date'   => '11-02', 'name' => "All Souls' Day",                    'local' => 'Finados' ],
      [ 'date'   => '11-15', 'name' => 'Republic Proclamation Day',         'local' => 'Proclamação da República' ],
      [ 'date'   => '11-20', 'name' => 'Zumbi and Black Consciousness Day', 'local' => 'Dia Nacional de Zumbi e da Consciência Negra', 'from' => 2024 ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',                     'local' => 'Natal' ]
    ]
  ];

?>

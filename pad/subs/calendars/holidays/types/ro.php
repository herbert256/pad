<?php

  // ro - Romania: the legal holidays of the Labour Code (Legea 53/2003), art. 139, with
  // Good Friday, Easter and Whitsun on the dates of the Orthodox church - the law gives an
  // employee of another Christian church its own, which the page has to ask for. The list
  // grew: Whit Sunday and Monday and the Dormition on 15 August since 2009 (Legea 202/2008),
  // St Andrew on 30 November since 2012 (Legea 147/2012), Union Day on 24 January and
  // Children's Day on 1 June since 2017 (Legea 176/2016, 220/2016), Good Friday since 2018
  // (Legea 64/2018, in force from 16 March), Epiphany and St John on 6 and 7 January since
  // 2024 (Legea 52/2023). The days off the government adds for its own staff around a holiday
  // and the two days of each religion other than the Christian ones are left out. The file
  // knows the years since 2009. Checked against the text of art. 139 and the amending laws,
  // the Orthodox Easter of the Julian computus against PHP's calendar extension for every
  // year from 1583 to 9999, and the dates of 2017, 2018, 2024, 2025 and 2026.

  return [
    'name'  => 'Romania',
    'langs' => [ 'ro' ],
    'years' => [ 2009, padHolidaysLast ],
    'days'  => [
      [ 'date'     => '01-01', 'name' => "New Year's Day",          'local' => 'Anul Nou' ],
      [ 'date'     => '01-02', 'name' => "Day after New Year's Day", 'local' => 'A doua zi de Anul Nou' ],
      [ 'date'     => '01-06', 'name' => 'Epiphany',                'local' => 'Botezul Domnului - Boboteaza', 'from' => 2024 ],
      [ 'date'     => '01-07', 'name' => 'Synaxis of St John the Baptist', 'local' => 'Soborul Sfântului Proroc Ioan Botezătorul', 'from' => 2024 ],
      [ 'date'     => '01-24', 'name' => 'Union Day',               'local' => 'Ziua Unirii Principatelor Române', 'from' => 2017 ],
      [ 'orthodox' => -2,      'name' => 'Good Friday',             'local' => 'Vinerea Mare', 'from' => 2018 ],
      [ 'orthodox' => 0,       'name' => 'Easter Sunday',           'local' => 'Paștele' ],
      [ 'orthodox' => 1,       'name' => 'Easter Monday',           'local' => 'A doua zi de Paști' ],
      [ 'date'     => '05-01', 'name' => 'Labour Day',              'local' => 'Ziua Muncii' ],
      [ 'date'     => '06-01', 'name' => "Children's Day",          'local' => 'Ziua Copilului', 'from' => 2017 ],
      [ 'orthodox' => 49,      'name' => 'Whit Sunday',             'local' => 'Rusaliile' ],
      [ 'orthodox' => 50,      'name' => 'Whit Monday',             'local' => 'A doua zi de Rusalii' ],
      [ 'date'     => '08-15', 'name' => 'Dormition of the Mother of God', 'local' => 'Adormirea Maicii Domnului' ],
      [ 'date'     => '11-30', 'name' => "St Andrew's Day",         'local' => 'Sfântul Apostol Andrei', 'from' => 2012 ],
      [ 'date'     => '12-01', 'name' => 'National Day',            'local' => 'Ziua Națională a României' ],
      [ 'date'     => '12-25', 'name' => 'Christmas Day',           'local' => 'Crăciunul' ],
      [ 'date'     => '12-26', 'name' => 'Second Day of Christmas', 'local' => 'A doua zi de Crăciun' ]
    ]
  ];

?>

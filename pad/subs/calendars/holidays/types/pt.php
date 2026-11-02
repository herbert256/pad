<?php

  // pt - Portugal: the thirteen mandatory holidays of the Código do Trabalho, art. 234, with
  // Easter Sunday among them. Corpus Christi, 5 October, 1 November and 1 December were not
  // held from 2013 to 2015 (Lei 23/2012) and are again since 2016 (Lei 8/2016). Carnival
  // Tuesday is an optional holiday by agreement only (art. 235) - an observance; Good Friday
  // may be observed on another day of local meaning around Easter, and the municipal holiday
  // of each town is left out. Regions by their ISO 3166-2 code: pt-20, the Azores - the Day
  // of the Autonomous Region on Whit Monday (Decreto Regional 13/80/A) - and pt-30, Madeira -
  // the Day of the Region on 1 July, the First Octave on 26 December and, since 2025, the
  // Day of Autonomy on 2 April (Decreto Legislativo Regional 17/2024/M). The file knows the
  // years from 2010. Checked against the text of art. 234 and 235 with the versions of Lei
  // 7/2009, 23/2012 and 8/2016, the Madeiran decree of 2024, and the dates of 2012, 2013,
  // 2016, 2025 and 2026 (Corpus Christi 4 June 2026, the Azores' day 25 May 2026 in the
  // University of the Azores' calendar).

  return [
    'name'    => 'Portugal',
    'langs'   => [ 'pt' ],
    'regions' => [ '20' => 'Azores', '30' => 'Madeira' ],
    'years'   => [ 2010, padHolidaysLast ],
    'days'    => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",              'local' => 'Ano Novo' ],
      [ 'easter' => -47,     'name' => 'Carnival',                    'local' => 'Carnaval', 'type' => 'observance' ],
      [ 'date'   => '04-02', 'name' => 'Day of Autonomy',             'local' => 'Dia da Autonomia', 'in' => [ '30' ], 'from' => 2025 ],
      [ 'easter' => -2,      'name' => 'Good Friday',                 'local' => 'Sexta-Feira Santa' ],
      [ 'easter' => 0,       'name' => 'Easter Sunday',               'local' => 'Domingo de Páscoa' ],
      [ 'date'   => '04-25', 'name' => 'Freedom Day',                 'local' => 'Dia da Liberdade' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',                  'local' => 'Dia do Trabalhador' ],
      [ 'easter' => 50,      'name' => 'Day of the Azores',           'local' => 'Dia da Região Autónoma dos Açores', 'in' => [ '20' ] ],
      [ 'easter' => 60,      'name' => 'Corpus Christi',              'local' => 'Corpo de Deus', 'to' => 2012 ],
      [ 'easter' => 60,      'name' => 'Corpus Christi',              'local' => 'Corpo de Deus', 'from' => 2016 ],
      [ 'date'   => '06-10', 'name' => 'Portugal Day',                'local' => 'Dia de Portugal, de Camões e das Comunidades Portuguesas' ],
      [ 'date'   => '07-01', 'name' => 'Day of Madeira',              'local' => 'Dia da Região Autónoma da Madeira', 'in' => [ '30' ] ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day',              'local' => 'Assunção de Nossa Senhora' ],
      [ 'date'   => '10-05', 'name' => 'Republic Day',                'local' => 'Implantação da República', 'to' => 2012 ],
      [ 'date'   => '10-05', 'name' => 'Republic Day',                'local' => 'Implantação da República', 'from' => 2016 ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",             'local' => 'Dia de Todos-os-Santos', 'to' => 2012 ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",             'local' => 'Dia de Todos-os-Santos', 'from' => 2016 ],
      [ 'date'   => '12-01', 'name' => 'Restoration of Independence', 'local' => 'Restauração da Independência', 'to' => 2012 ],
      [ 'date'   => '12-01', 'name' => 'Restoration of Independence', 'local' => 'Restauração da Independência', 'from' => 2016 ],
      [ 'date'   => '12-08', 'name' => 'Immaculate Conception',       'local' => 'Imaculada Conceição' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',               'local' => 'Natal' ],
      [ 'date'   => '12-26', 'name' => 'First Octave',                'local' => 'Primeira Oitava', 'in' => [ '30' ] ]
    ]
  ];

?>

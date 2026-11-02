<?php

  // A vacancy as a database answers it: its employments as a text, its pay a range of a month,
  // a remote that says no - and the note of the hiring manager, which never reaches the page.

  $vacancy = [ 'title' => 'Baker', 'description' => '<p>Bread from 05:00.</p><ul><li>Full time</li></ul>',
               'datePosted' => '2026-11-01', 'expires' => '2026-12-01T00:00', 'employment' => 'full time, Part-Time',
               'company' => 'The Loaf', 'companyurl' => 'https://loaf.example', 'logo' => '/img/loaf.png',
               'street' => 'Main Street 1', 'city' => 'Utrecht', 'postcode' => '3511 AA', 'country' => 'NL',
               'remote' => 'no', 'salary' => '3200-3800', 'currency' => 'eur', 'per' => 'month',
               'note' => 'Offer 3500 at most' ];

?>

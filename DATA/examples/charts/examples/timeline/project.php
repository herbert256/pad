<?php

  // A plan of phases - a start and an end - and milestones - a day.

  $plan = [
    [ 'name' => 'Research',      'kind' => 'Phase',     'start' => '2026-01-05', 'end' => '2026-02-13', 'day' => '', 'note' => '' ],
    [ 'name' => 'Design',        'kind' => 'Phase',     'start' => '2026-02-02', 'end' => '2026-03-27', 'day' => '', 'note' => '' ],
    [ 'name' => 'Build',         'kind' => 'Phase',     'start' => '2026-03-16', 'end' => '2026-06-12', 'day' => '', 'note' => '' ],
    [ 'name' => 'Content',       'kind' => 'Phase',     'start' => '2026-04-20', 'end' => '2026-06-19', 'day' => '', 'note' => '' ],
    [ 'name' => 'Testing',       'kind' => 'Phase',     'start' => '2026-05-25', 'end' => '2026-06-26', 'day' => '', 'note' => '' ],
    [ 'name' => 'Kick-off',      'kind' => 'Milestone', 'start' => '', 'end' => '', 'day' => '2026-01-05', 'note' => 'Goals and budget agreed' ],
    [ 'name' => 'Design signed', 'kind' => 'Milestone', 'start' => '', 'end' => '', 'day' => '2026-03-27', 'note' => 'The board approves the look' ],
    [ 'name' => 'Beta',          'kind' => 'Milestone', 'start' => '', 'end' => '', 'day' => '2026-05-15', 'note' => 'Open to staff' ],
    [ 'name' => 'Launch',        'kind' => 'Release',   'start' => '', 'end' => '', 'day' => '2026-07-01', 'note' => 'The new site goes live' ],
  ];

?>

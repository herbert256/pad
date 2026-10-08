<?php

  // What the islands start with. Each member's row carries its card - the props of one
  // island - and the stats are one more island's; the template writes them with the ^
  // sigil, as JSON escaped for the attribute.

  $team = reactData ( 'team' );
  $most = max ( array_column ( $team, 'commits' ) );

  $members = [];

  foreach ( $team as $member )
    $members [] = [ 'name' => $member ['name'],
                    'role' => $member ['role'],
                    'card' => $member + [ 'most' => $most ] ];

  $stats = [ 'people'  => count ( $team ),
             'commits' => array_sum ( array_column ( $team, 'commits' ) ),
             'cities'  => count ( array_unique ( array_column ( $team, 'city' ) ) ),
             'teams'   => count ( array_unique ( array_column ( $team, 'team' ) ) ) ];

?>

<?php

  // {jsonld:video data='video'/} or {jsonld:video name='Making pea soup', thumbnail='soup.jpg',
  // uploaded='2026-11-01T10:00:00+01:00', duration='4 min 12 s', player=$embed/} - a
  // VideoObject: a video of the page, what it shows, when it was put there, how long it runs
  // and where it plays.
  //
  // The fields, by their PAD name and the Schema.org property each fills - the required and
  // recommended properties of Google's video rich result: name (name, required), thumbnail
  // (thumbnailUrl, required - one address or more, a list or a text with a comma between
  // them), uploaded (uploadDate, a date, required - with its offset, or Google takes its own
  // time zone), description (description), duration (duration, PT4M12S or 4 min 12 s), file
  // (contentUrl, the address of the video file itself, which Google prefers), player
  // (embedUrl, the address of its player, where there is no file), expires (expires, a
  // date, when the video goes - left out when it stays), author (author, a Person by its
  // name) and views (interactionStatistic, how many times it was watched - an
  // InteractionCounter of the WatchAction). Not content=: that is the option of every tag.

  return [

    'type'    => 'VideoObject',

    'fields'  => [
      'name'        => [ 'name',                                       'text',     TRUE ],
      'thumbnail'   => [ 'thumbnailUrl',                               'urls',     TRUE ],
      'uploaded'    => [ 'uploadDate',                                 'date',     TRUE ],
      'description' => [ 'description',                                'text'           ],
      'duration'    => [ 'duration',                                   'duration'       ],
      'file'        => [ 'contentUrl',                                 'url'            ],
      'player'      => [ 'embedUrl',                                   'url'            ],
      'expires'     => [ 'expires',                                    'date'           ],
      'author'      => [ 'author.name',                                'text'           ],
      'views'       => [ 'interactionStatistic.userInteractionCount',  'count'          ],
    ],

    'objects' => [ 'author'               => 'Person',
                   'interactionStatistic' => [ '@type' => 'InteractionCounter', 'interactionType' => [ '@type' => 'WatchAction' ] ] ],

  ];

?>

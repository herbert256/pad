<?php

  // Descriptions and syntax for the subsystem namespaces registered by the engine.

  function subsystemsInfo () {

    return [
      'map' => [ 'Choropleths, markers and proportional bubbles.', 'subsystems', "{map:europe width=540/}" ],
      'holidays' => [ 'Country and regional public holidays.', 'subsystems', "{holidays:nl year=2026/}" ],
      'search' => [ 'Ranked search, exact filters and facets.', 'subsystems', "{search:en 'documents', q='PAD'/}" ],
      'ai' => [ 'Text and validated JSON responses.', 'subsystems', "{ai:anthropic 'Name three rivers.'/}" ],
      'format' => [ 'Read documents into named data stores.', 'subsystems', "{format:json 'rows'}[1,2,3]{/format:json}" ],
      'sheet' => [ 'Formula grids computed on the server.', 'subsystems', "{sheet:grid}Name,Amount\nOne,12\nTwo,=B2*2{/sheet:grid}" ],

      'chart'    => [ 'A chart type, a file of pad/subs/charts/types/, drawn as inline SVG.', 'subsystems',
                      "{chart:pie data='rows', label='name', value='amount'/}\n{chart:pie data='rows', ...}\n{chart:pie data='rows', .../}" ],
      'game'     => [ 'A game, a file of pad/subs/games/types/, played in the browser.', 'subsystems',
                      "{game:memory pairs=6/}\n{game:memory/}\n{game:memory/}" ],
      'toy'      => [ 'A toy, a file of pad/subs/toys/types/ - a live simulation in the browser.', 'subsystems',
                      "{toy:life pattern='glider'/}\n{toy:life/}\n{toy:life/}" ],
      'diagram'  => [ 'A diagram type, a file of pad/subs/diagrams/types/, laid out on the server.', 'subsystems',
                      "{diagram:state}...{/diagram:state}\n{diagram:state}...{/diagram:state}" ],
      'widget'   => [ 'A widget, a file of pad/subs/widgets/types/, that works without JavaScript.', 'subsystems',
                      "{widget:tabs}{widget:tab 'One'}...{/widget:tab}{/widget:tabs}\n{widget:tabs}...{/widget:tabs}\n{widget:tabs}...{/widget:tabs}" ],
      'barcode'  => [ 'A barcode symbology, a file of pad/subs/codes/types/, encoded on the server.', 'subsystems',
                      "{barcode:ean13 '400638133393'/}\n{barcode:ean13 '400638133393'/}" ],
      'embed'    => [ 'An embed provider, a file of pad/subs/embeds/types/ - loaded only on a click.', 'subsystems',
                      "{embed:youtube 'aqz-KE-bpKQ', title='Big Buck Bunny'/}\n{embed:auto 'https://youtu.be/aqz-KE-bpKQ'/}\n{embed:youtube 'aqz-KE-bpKQ'/}" ],
      'input'    => [ 'A rich form field, a file of pad/subs/inputs/types/, posted by any form.', 'subsystems',
                      "{input:slider 'volume', label='Volume'/}" ],
      'icon'     => [ 'An icon, a file of pad/subs/icons/types/, drawn as inline SVG - never bare.', 'subsystems',
                      "{icon:star size=24/}\n{icon:star size=24/}" ],
      'fake'     => [ 'A fake kind, a file of pad/subs/fakes/types/ - the same values for the same seed, never bare.', 'subsystems',
                      "{fake:email/}\n{fake:email/}\n{fake:rows 'name, email', rows=3}...{/fake:rows}" ],
      'pattern'  => [ 'A pattern, a file of pad/subs/patterns/types/ - a seeded SVG background, never bare.', 'subsystems',
                      "{pattern:topography seed=7/}\n{pattern:topography seed=7/}" ],
      'sound'    => [ 'A sound, a file of pad/subs/sounds/types/ - a recipe the browser plays and the server writes as a WAV, never bare.', 'subsystems',
                      "{sound:chime/}\n{sound:chime/}\n{sound:pop on='click', target='#save'/}" ],
      'printable' => [ 'A printable, a file of pad/subs/printables/types/ - pages made for paper, the same on the screen, printed and as a PDF, never bare.', 'subsystems',
                      "{printable:labels data='rows', sheet='a4-3x8'}...{/printable:labels}\n{printable:labels data='rows'}...{/printable:labels}" ],
      'diff' => [ 'A comparison of texts, JSON or keyed records from pad/subs/diffs/types/.', 'subsystems',
                  "{diff:words \$old, \$new/}\n{diff:json \$old, \$new/}\n{diff:rows \$old, \$new, key='id'/}" ],
      'table' => [ 'A table of rows, grouped records, a pivot or a matrix from pad/subs/tables/types/.', 'subsystems',
                   "{table:standard data='orders', sortable/}\n{table:grouped data='orders', group='region', totals='amount'/}" ],
      'time' => [ 'Time displays from pad/subs/times/types/ - countdown, relative time, world clocks and stopwatches.', 'subsystems',
                      "{time:countdown '2026-12-31', live/}\n{time:relative '2026-11-01'/}\n{time:clock timezone='Europe/Amsterdam'/}\n{time:stopwatch/}" ],
      'timeline' => [ 'Event views from pad/subs/timelines/types/ - horizontal, vertical, roadmap and swimlane.', 'subsystems',
                      "{timeline:horizontal data='events'/}\n{timeline:roadmap data='events', lane='team'/}\n{timeline:swimlane data='events', lane='team'/}" ],

      'image' => [ 'Local images as thumbnails, responsive pictures, captioned figures or contact sheets.', 'subsystems',
                   "{image:thumbnail 'photo.jpg', width=320, alt='A landscape'/}\n{image:picture 'photo.jpg', widths='320,640,960', alt='A landscape'/}" ],
      'avatar' => [ 'An identity picture: initials, identicons, rings, mosaic, pixelbot, Gravatar, geometric art or a photograph with a local fallback.', 'subsystems',
                    "{avatar:initials 'Ada Lovelace'/}\n{avatar:geometric 'ada', style='rings'/}" ],
      'calendar' => [ 'A Gregorian calendar view from pad/subs/calendars/types/ - days, weeks, months, quarters, years and agendas.', 'subsystems',
                      "{calendar:month '2026-11'/}\n{calendar:week '2026-11-02'/}\n{calendar:year '2026'/}" ],
      'notation' => [ 'A notation, a file of pad/subs/notations/types/ - a formula, a tune, a word or a position drawn on the server.', 'subsystems',
                      "{notation:braille 'Hello'/}\n{notation:braille 'Hello'/}\n{notation:math}a/b{/notation:math}" ],
      'section'  => [ 'A section of a landing page, a file of pad/subs/sections/types/ - pure CSS, coloured by the theme, never bare.', 'subsystems',
                      "{section:hero title='Build faster'}...{/section:hero}\n{section:hero title='Build faster'}...{/section:hero}" ],
      'effect'   => [ 'An effect, a file of pad/subs/effects/types/ - motion and decoration, CSS first, still under reduced motion.', 'subsystems',
                      "{effect:typewriter}Hello{/effect:typewriter}\n{effect:typewriter}Hello{/effect:typewriter}" ],
      'jsonld'   => [ 'A JSON-LD kind, a file of pad/subs/jsonld/types/ - the structured data of a page as a Schema.org object, never bare.', 'subsystems',
                      "{jsonld:article headline=\$title, author=\$author/}\n{jsonld:article data='post'/}\n{jsonld:faq data='questions'/}" ],

    ];

  }

  function subsystemsGroups () {

    return [
      'subsystems' => [ 'Tag subsystems', 'Kinds selected with a tag prefix' ],
      'kinds' => [ 'Options and helpers', 'Kinds selected by options, PHP helpers or spreadsheet formulas' ],
    ];

  }

?>

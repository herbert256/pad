<?php

  // The showcase: the chart families, each a pulldown of the menu and a section of the
  // home page, and their examples. An example is a page of its own - family/name.pad holds
  // the {chart} tag, and its data is the paired family/name.php, the _data file the tag
  // names, or the content of a {chart}...{/chart} pair - so what the cells show is the
  // page's own source, never a copy.

  function chartsCatalog () {

    return [

      'line' => [ 'Line', 'Change over time', [
        'line/visits'       => [ 'Website visits',            'One series, the last point marked - the data JSON, between the tags.' ],
        'line/temperatures' => [ 'Temperatures in three cities', 'A list of fields in value= draws a line each, named at its end.' ],
        'line/shares'       => [ 'A share price over a year', 'Twelve months of a stock, from a YAML file.' ] ] ],

      'area' => [ 'Area & stream', 'Amounts adding up over time', 'Area', [
        'area/energy'  => [ 'Electricity by source',       'stacked lays the areas on top of each other, the top edge the total.' ],
        'area/devices' => [ 'Visits by device',            'percent stacks every year to 100% - the shift from desktop to mobile.' ],
        'area/music'   => [ 'Two years of listening',      'stream: the genres swell and shrink around a wandering middle, in smooth curves.' ],
        'area/cities'  => [ 'Temperatures in nine cities', 'multiples: a small chart per city, all on one scale, in a grid.' ] ] ],

      'bars' => [ 'Bars', 'Categories compared', [
        'bars/sales'     => [ 'Sales per month',          'Columns from a zero baseline - the data CSV, between the tags.' ],
        'bars/channels'  => [ 'Online against the shop',  'Several series side by side, a legend above.' ],
        'bars/energy'    => [ 'The energy mix per year',  'stacked puts the series on top of each other.' ],
        'bars/profit'    => [ 'Profit and loss',          'Negative values hang below the baseline.' ],
        'bars/languages' => [ 'The most spoken languages', 'hbar lays the bars down - the data XML, between the tags.' ],
        'bars/survey'    => [ 'A survey, answer by answer', 'Horizontal and stacked: every answer a part of the bar.' ],
        'bars/waterfall' => [ 'From revenue to profit',   'waterfall: each step floats from where the one before ended.' ] ] ],

      'funnel' => [ 'Funnel & pyramid', 'Steps that narrow', 'Funnel', [
        'funnel/shop'       => [ 'A web shop funnel',    'From visit to payment - the share of the first step and of the step before.' ],
        'funnel/population' => [ 'A population pyramid', 'Two fields back to back: men left, women right, by age group.' ],
        'funnel/food'       => [ 'A food pyramid',       'One field: a triangle in layers, each layer\'s area its share.' ] ] ],

      'pie' => [ 'Pie & donut', 'Parts of a whole', 'Pie', [
        'pie/browsers' => [ 'Browser market share', 'Slices clockwise from twelve o\'clock, the share in the legend.' ],
        'pie/budget'   => [ 'A monthly budget',     'A donut shows the total in its middle.' ],
        'pie/fruit'    => [ 'Eleven kinds of fruit', 'Past eight slices the smallest are folded into a grey Other.' ] ] ],

      'parts' => [ 'Waffle, marimekko & more', 'More ways to part a whole', 'Parts', [
        'parts/energy'    => [ 'Where a home\'s energy goes',    'waffle: 100 squares shared out by the largest remainder.' ],
        'parts/market'    => [ 'Phones by region and brand',     'marimekko: each column as wide as its region, the brands to 100% inside.' ],
        'parts/house'     => [ 'A house of 150 seats',           'parliament: a dot per seat, the parties from left to right.' ],
        'parts/languages' => [ 'Languages 1,000 students speak', 'venn: three sets, every region with its own count.' ],
        'parts/pets'      => [ 'Cats, dogs or both',             'venn: two circles whose overlap has the area of the homes with both.' ] ] ],

      'scatter' => [ 'Scatter & bubble', 'How two numbers relate', 'Scatter', [
        'scatter/athletes'  => [ 'Height against weight', 'A dot per row, and trend draws the least-squares line.' ],
        'scatter/flowers'   => [ 'Three kinds of flowers', 'color= colours the dots by a field.' ],
        'scatter/countries' => [ 'Income, life and people', 'A bubble\'s area is its size= - here the population.' ] ] ],

      'heatmap' => [ 'Heatmap', 'Patterns in dense data', [
        'heatmap/traffic'  => [ 'Traffic by day and hour', 'Data made by a PHP loop - seven steps of one hue.' ],
        'heatmap/rainfall' => [ 'Rainfall per month',      'Cities against months, the value in every cell that has room.' ],
        'heatmap/commits'  => [ 'A year of commits',       'calendar: a square per day, a column per week, the months above.' ] ] ],

      'gauge' => [ 'Gauge & radar', 'Measures at a glance', 'Gauge', [
        'gauge/disk'     => [ 'Disk in use',             'One number on a half ring, coloured bands and a target.' ],
        'gauge/servers'  => [ 'Three servers',           'A gauge per row - the value and label of each from the data.' ],
        'radar/phones'   => [ 'Three phones compared',   'radar: a spoke per measure, a polygon per phone.' ],
        'radar/players'  => [ 'Two midfielders',         'Six skills from a YAML file, on a scale of 0 to 100.' ] ] ],

      'radial' => [ 'Rose & radial bars', 'Values around a centre', 'Radial', [
        'radial/nightingale' => [ 'Nightingale\'s rose',   'Deaths in the Crimean war by month - each wedge\'s area its value, three causes as rings.' ],
        'radial/wind'        => [ 'A wind rose',           'Sixteen compass points, north on top - one field, one colour.' ],
        'radial/funds'       => [ 'Raised towards a goal', 'radialbar: a ring per class, the arc its share of to=.' ] ] ],

      'distribution' => [ 'Distributions', 'How values spread', 'Spread', [
        'distribution/heights'  => [ 'Heights of 400 adults',  'histogram: two bell curves together give two humps.' ],
        'distribution/delivery' => [ 'Delivery times',         'Whole days are counted in bins of whole days.' ],
        'distribution/salaries' => [ 'Salary per team',        'boxplot: quartiles, median, whiskers and the outlier.' ],
        'distribution/reaction' => [ 'Reaction times, rested or not', 'density: a smooth curve per group over one axis, the medians dashed.' ],
        'distribution/scores'   => [ 'Exam scores per class',  'violin: the shape of each class, quartiles and median inside - a split class shows its two humps.' ] ] ],

      'measure' => [ 'Bullet, rings & areas', 'Measures against goals', 'Measure', [
        'measure/kpis'      => [ 'A quarter against its targets', 'bullet: the value as a bar, the target a line, grey bands from poor to good.' ],
        'measure/activity'  => [ 'Today\'s activity',             'rings: a progress ring per goal, a goal from a field of each row.' ],
        'measure/projects'  => [ 'Five projects, per cent done',  'Rings against the default goal of 100, the share in the legend.' ],
        'measure/emissions' => [ 'CO₂ per country',               'proportional: a circle\'s area is the value, wrapping into a second line.' ],
        'measure/oceans'    => [ 'The five oceans',               'square draws squares instead, on one baseline.' ] ] ],

      'gantt' => [ 'Gantt', 'Tasks in time', [
        'gantt/website' => [ 'A new website',          'Dates from CSV, the part done drawn full, today marked.' ],
        'gantt/release' => [ 'A release by team',      'Weeks as plain numbers, the bars coloured by team.' ] ] ],

      'finance' => [ 'Finance', 'Prices and two measures at once', [
        'finance/share'  => [ 'A share over thirty days', 'candlestick: open, high, low and close a day, and the volume below.' ],
        'finance/margin' => [ 'Revenue and margin',       'dual: bars on the left axis, the margin as a line on the right one.' ] ] ],

      'time' => [ 'Spiral', 'Cycles in a long series', [
        'time/icecream' => [ 'Five years of ice cream', 'spiral: a turn a year, every summer a dark band on the same spoke.' ],
        'time/visits'   => [ 'Visits day by day',       'period=7 winds a turn a week - the quiet weekends line up.' ] ] ],

      'sankey' => [ 'Sankey', 'Flows from stage to stage', [
        'sankey/energy'  => [ 'Where energy goes',  'Sources, conversion and use - each band as wide as its flow.' ],
        'sankey/funnel'  => [ 'A web shop funnel',  'Visitors on their way to an order - the data YAML, between the tags.' ],
        'sankey/salary'  => [ 'A salary, spent',    'One income split over posts, then over sub-posts.' ] ] ],

      'network' => [ 'Network', 'Connections', [
        'network/friends' => [ 'A circle of friends', 'The force layout pulls linked nodes together.' ],
        'network/flights' => [ 'Flights between hubs', 'layout=\'radial\' sets the nodes on a circle.' ],
        'network/modules' => [ 'Who imports whom',     'arrows show the direction, value= the weight of a link.' ] ] ],

      'relations' => [ 'Chord, arc & parallel', 'Flows, links and many measures', 'Relations', [
        'relations/migration' => [ 'Moving between regions',        'chord: an arc per region, a ribbon per move as wide as the people moving.' ],
        'relations/coauthors' => [ 'Papers written together',       'arc: the authors on a line, a half circle per pair, thicker for more papers.' ],
        'relations/cars'      => [ 'Thirty-two cars, six measures', 'parallel: an axis per measure, a line per car, coloured by origin.' ] ] ],

      'hierarchy' => [ 'Trees & packs', 'Parts of a hierarchy', 'Trees', [
        'hierarchy/company' => [ 'A company budget',   'levels= nests the posts in their departments.' ],
        'hierarchy/disk'    => [ 'What fills the disk', 'A flat treemap: one level, one colour.' ],
        'hierarchy/world'   => [ 'The world\'s people', 'A sunburst: continents inside, regions around them.' ],
        'hierarchy/files'       => [ 'The files of a project', 'pack: circles in circles, a folder around its files, sized by kilobytes.' ],
        'hierarchy/company_org' => [ 'Who reports to whom',    'orgchart: a box per person under their boss, the job title as a second line.' ] ] ],

      'sparkline' => [ 'Sparkline', 'A chart the size of a word', [
        'sparkline/stocks' => [ 'Shares in a table', 'Word-sized lines in table cells, no axes.' ] ] ],

    ];

  }

?>

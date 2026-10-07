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

      'bars' => [ 'Bars', 'Categories compared', [
        'bars/sales'     => [ 'Sales per month',          'Columns from a zero baseline - the data CSV, between the tags.' ],
        'bars/channels'  => [ 'Online against the shop',  'Several series side by side, a legend above.' ],
        'bars/energy'    => [ 'The energy mix per year',  'stacked puts the series on top of each other.' ],
        'bars/profit'    => [ 'Profit and loss',          'Negative values hang below the baseline.' ],
        'bars/languages' => [ 'The most spoken languages', 'hbar lays the bars down - the data XML, between the tags.' ],
        'bars/survey'    => [ 'A survey, answer by answer', 'Horizontal and stacked: every answer a part of the bar.' ],
        'bars/waterfall' => [ 'From revenue to profit',   'waterfall: each step floats from where the one before ended.' ] ] ],

      'pie' => [ 'Pie & donut', 'Parts of a whole', 'Pie', [
        'pie/browsers' => [ 'Browser market share', 'Slices clockwise from twelve o\'clock, the share in the legend.' ],
        'pie/budget'   => [ 'A monthly budget',     'A donut shows the total in its middle.' ],
        'pie/fruit'    => [ 'Eleven kinds of fruit', 'Past eight slices the smallest are folded into a grey Other.' ] ] ],

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

      'distribution' => [ 'Histogram & box plot', 'How values spread', 'Spread', [
        'distribution/heights'  => [ 'Heights of 400 adults',  'histogram: two bell curves together give two humps.' ],
        'distribution/delivery' => [ 'Delivery times',         'Whole days are counted in bins of whole days.' ],
        'distribution/salaries' => [ 'Salary per team',        'boxplot: quartiles, median, whiskers and the outlier.' ] ] ],

      'gantt' => [ 'Gantt', 'Tasks in time', [
        'gantt/website' => [ 'A new website',          'Dates from CSV, the part done drawn full, today marked.' ],
        'gantt/release' => [ 'A release by team',      'Weeks as plain numbers, the bars coloured by team.' ] ] ],

      'sankey' => [ 'Sankey', 'Flows from stage to stage', [
        'sankey/energy'  => [ 'Where energy goes',  'Sources, conversion and use - each band as wide as its flow.' ],
        'sankey/funnel'  => [ 'A web shop funnel',  'Visitors on their way to an order - the data YAML, between the tags.' ],
        'sankey/salary'  => [ 'A salary, spent',    'One income split over posts, then over sub-posts.' ] ] ],

      'network' => [ 'Network', 'Connections', [
        'network/friends' => [ 'A circle of friends', 'The force layout pulls linked nodes together.' ],
        'network/flights' => [ 'Flights between hubs', 'layout=\'radial\' sets the nodes on a circle.' ],
        'network/modules' => [ 'Who imports whom',     'arrows show the direction, value= the weight of a link.' ] ] ],

      'hierarchy' => [ 'Treemap & sunburst', 'Parts of a hierarchy', 'Treemap', [
        'hierarchy/company' => [ 'A company budget',   'levels= nests the posts in their departments.' ],
        'hierarchy/disk'    => [ 'What fills the disk', 'A flat treemap: one level, one colour.' ],
        'hierarchy/world'   => [ 'The world\'s people', 'A sunburst: continents inside, regions around them.' ] ] ],

      'sparkline' => [ 'Sparkline', 'A chart the size of a word', [
        'sparkline/stocks' => [ 'Shares in a table', 'Word-sized lines in table cells, no axes.' ] ] ],

    ];

  }

?>

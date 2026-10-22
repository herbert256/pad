<?php

  // The examples of the showcase by group, in the order the menu, the overview and the
  // Previous / Next links show them: each a page of this application - its .pad and, when it
  // declares something of its own, its .php - with a title and a few words on what it shows.

  function psGroups () {

    return [
      'Tables'     => 'A declared table is a tag',
      'Relations'  => 'Nested tables follow their relations',
      'Views'      => 'Tables that stand on another',
      'Joins'      => 'More than one table in a row',
      'Aggregates' => 'Groups, totals and distinct rows',
      'Paging'     => 'The SQL limit and the pager',
      'Unions'     => 'Rows of several tables in one list',
      'Output'     => 'Other shapes of the answer'
    ];

  }

  function psCatalog () {

    return [

      'Tables' => [
        'tables/tag'    => [ 'A table is a tag',
                             'Declared in $padSelect, a table is a tag of its own name: the pair walks its rows, sorted by the declared order, and every column is a field.' ],
        'tables/prefix' => [ 'The select: prefix',
                             'select: names the type, for a table whose name is something else as well - here a data store of the page called labels too.' ],
        'tables/key'    => [ 'A row by its key',
                             '$field=value on the tag binds a field of the declared key - one field, or both of a key of two. The value goes into the SQL escaped, never as SQL.' ],
        'tables/where'  => [ 'where= with bound values',
                             'where= is SQL the template writes, as a quoted string; a value goes into it as $name and is bound as an escaped literal of that variable.' ],
        'tables/order'  => [ 'order= and rows=',
                             'order= sorts in the SQL and rows= is its limit, so the longest five tracks are the five longest of them all, not five sorted afterwards.' ],
        'tables/fields' => [ 'fields= : the columns',
                             'fields= on the tag is the column list as SQL, with expressions and aliases; declared as an array it is column => alias.' ],
        'tables/db'     => [ 'db= : a tag of its own name',
                             'The tag name is yours, db= names the table it reads - two tags over one table, each with its own order and where.' ],
        'tables/else'   => [ 'No rows: @else@',
                             'A select that finds nothing renders its @else@ part, as every tag with no rows does.' ]
      ],

      'Relations' => [
        'relations/down'        => [ 'Parent to children',
                                     'Nested in {artists}, {albums} reads only the albums of the artist of the row: the relation albums - artists says which field they share.' ],
        'relations/up'          => [ 'Child to parent',
                                     'The same relation the other way round: nested in an album, {artists} is the one artist the album belongs to.' ],
        'relations/deep'        => [ 'Three levels deep',
                                     'Artists, their albums, the tracks of each album - every level follows the relation to the level around it.' ],
        'relations/key_form'    => [ 'A relation by the other key',
                                     "[ 'key' => 'signed_to' ]: the field of artists that holds the declared key of labels. An artist with no label relates to no label." ],
        'relations/field_map'   => [ 'A relation over two fields',
                                     'A playlist entry points at a track by album and track number: the relation maps both fields, and the chain goes on to the album and its artist.' ],
        'relations/self'        => [ 'A table related to itself',
                                     'Two views over genres, one for the parent and one for the sub-genres, related to genres by parent_code.' ],
        'relations/every_level' => [ 'Every enclosing select counts',
                                     'Albums inside an artist inside a genre: the albums follow both relations, so each artist shows only the albums of that genre.' ],
        'relations/count'       => [ 'Counting related rows',
                                     "fields='count(*) as n' on a nested table counts the related rows in the SQL." ]
      ],

      'Views' => [
        'views/base'         => [ 'A view over a table',
                                  'base= makes a virtual table: the base table with a where and an order of its own, used as a tag like any table.' ],
        'views/view_of_view' => [ 'A view of a view',
                                  'A view can stand on another view: the chain is followed to the table at its end, and the where of every view holds.' ],
        'views/nested'       => [ 'A view follows its base relations',
                                  'Nested in {artists}, the view of recent albums is related by the relation of albums, the table it stands on.' ],
        'views/narrowed'     => [ 'where= on a view narrows it',
                                  "A where= on the tag of a view holds beside the view's own: the recent albums that are under 14, never an album the view leaves out." ]
      ],

      'Joins' => [
        'joins/inner'   => [ 'Inner join',
                             'join= in the declaration: the joined table, its key met by key=, and the columns it adds - its declared fields, column => alias.' ],
        'joins/left'    => [ 'Left join',
                             'A left join keeps the row that has no partner: the artist without a label is listed, its label NULL.' ],
        'joins/natural' => [ 'Natural join',
                             'A natural join meets the columns both tables have - genre_code - with no key to give.' ],
        'joins/many'    => [ 'More than one join',
                             'A list of joins: a track with the title of its album and the name of the artist, in one statement.' ],
        'joins/view'    => [ 'A join to a view',
                             "A joined table with a where of its own joins only its rows: it stands in the statement as a derived table under its table's name." ],
        'joins/tag'     => [ 'join= on the tag',
                             'On the tag join= is the SQL after the word join, written as a quoted string.' ]
      ],

      'Aggregates' => [
        'aggregates/group'    => [ 'group= : a row per group',
                                   'group= with aggregate fields: the albums per genre, their number and their average price.' ],
        'aggregates/having'   => [ 'having= : a condition on the groups',
                                   'having= keeps the groups that pass - the artists with more than one album - and a nested table follows the relation from the grouped row.' ],
        'aggregates/rollup'   => [ 'rollup : the total row',
                                   'rollup adds the row of the grand total, with NULL in the grouped column.' ],
        'aggregates/distinct' => [ 'distinct',
                                   'distinct leaves out the rows that are the same as one before - every country once.' ]
      ],

      'Paging' => [
        'paging/rows_page' => [ 'rows= and page=',
                                'rows= and page= are the SQL limit: page 2 of five is the sixth to the tenth track, read from the database and nothing more.' ],
        'paging/pager'     => [ 'The pager',
                                '{pager} after the paged table counts its rows with one more statement - only when it is there - and links the pages.' ]
      ],

      'Unions' => [
        'unions/union' => [ 'union= : rows from two tables',
                            'The albums and the singles in one list: a union member is a declared table, built from its declaration alone.' ],
        'unions/many'  => [ 'A union of several',
                            'union= takes a list of tables; the order of the tag sorts the whole union.' ],
        'unions/member_rows' => [ 'A member with rows of its own',
                                  'A union member declared with rows= - and the order that picks them - adds only those: the albums since 2016 and the two newest singles.' ]
      ],

      'Output' => [
        'output/record'   => [ "type='record' and type='field'",
                               'type= is the shape of the answer: every row, one row, or one value.' ],
        'output/json'     => [ 'htmlAttrJson: JSON for an attribute',
                               'The rows as JSON, escaped for an HTML attribute - a table handed straight to a component in the browser.' ],
        'output/handling' => [ 'Handling options after the SQL',
                               'The rows a select answers go through the handling options of every tag: sort=, first=, the properties of the loop.' ]
      ]

    ];

  }

?>

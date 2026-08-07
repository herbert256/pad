<?php

  // Level and occurrence variables as the manual presents them.
  //
  // These are the examples the manual shows, not its prose pages: each is a file the manual
  // embeds with {example}, carried over with the answer it is supposed to give. The manual
  // application is not changed - an example lives in both places until you decide otherwise.

  return [

    [ 'lvl_occ/1',
      <<<'PAD'
      <table border='1'>
        {row}
          <tr>
            {column}
              <td>{$cell}</td>
            {/column}
          </tr>
        {/row}
      </table>
      PAD,
      '<table border=\'1\'><tr><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td></tr><tr><td>21</td><td>22</td><td>23</td><td>24</td><td>25</td></tr><tr><td>31</td><td>32</td><td>33</td><td>34</td><td>35</td></tr></table>',
      [
        'row' => [
          '1' => [
            'column' => [
              '1' => [
                'cell' => 11
              ],
              '2' => [
                'cell' => 12
              ],
              '3' => [
                'cell' => 13
              ],
              '4' => [
                'cell' => 14
              ],
              '5' => [
                'cell' => 15
              ]
            ]
          ],
          '2' => [
            'column' => [
              '1' => [
                'cell' => 21
              ],
              '2' => [
                'cell' => 22
              ],
              '3' => [
                'cell' => 23
              ],
              '4' => [
                'cell' => 24
              ],
              '5' => [
                'cell' => 25
              ]
            ]
          ],
          '3' => [
            'column' => [
              '1' => [
                'cell' => 31
              ],
              '2' => [
                'cell' => 32
              ],
              '3' => [
                'cell' => 33
              ],
              '4' => [
                'cell' => 34
              ],
              '5' => [
                'cell' => 35
              ]
            ]
          ]
        ]
      ] ],

  ];

?>
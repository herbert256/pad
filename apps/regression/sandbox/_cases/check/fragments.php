<?php

  // The fragments the manual's Ignore and Pipes pages embed, small enough to assert whole.
  //
  // Each case is the page as the manual renders it, with the answer it is supposed to give
  // stated here instead of left to a stored copy of the HTML.

  return [

    [ 'fragments/ignore_1',
      <<<'PAD'
      {ignore}
        <script>
          const user = {
            name: 'Alice',
            role: 'Developer',
            active: true
          };
      
          if (user.active) {
            console.log('User: ' + user.name + ' (' + user.role + ')');
          }
        </script>
      {/ignore}
      
      <p>JavaScript executed. Check browser console for output.</p>
      PAD,
      '<script>const user = {name: \'Alice\',role: \'Developer\',active: true};if (user.active) {console.log(\'User: \' + user.name + \' (\' + user.role + \')\');}</script><p>JavaScript executed. Check browser console for output.</p>' ],

    [ 'fragments/ignore_2',
      <<<'PAD'
      {ignore}<style>
        .example-box {
          border: 2px solid #4CAF50;
          padding: 20px;
          margin: 10px 0;
          border-radius: 8px;
          background: #f0f8ff;
        }
      
        .example-box h4 {
          margin: 0 0 10px 0;
          color: #4CAF50;
        }
      </style>{/ignore}
      
      <div class="example-box">
        <h4>Styled Box</h4>
        <p>This box uses the CSS defined above.</p>
      </div>
      PAD,
      '<style>.example-box {border: 2px solid #4CAF50;padding: 20px;margin: 10px 0;border-radius: 8px;background: #f0f8ff;}.example-box h4 {margin: 0 0 10px 0;color: #4CAF50;}</style><div class="example-box"><h4>Styled Box</h4><p>This box uses the CSS defined above.</p></div>' ],

    [ 'fragments/ignore_4',
      <<<'PAD'
      <div id="ignore-example-4" data-users="{echo $usersJson | ignore}"></div>
      
      {ignore}<script>
        const element = document.getElementById('ignore-example-4');
        const users = JSON.parse(element.dataset.users);
      
        const html = '<ul>' + users.map(user =>
          '<li><strong>' + user.name + '</strong> - ' + user.role + '</li>'
        ).join('') + '</ul>';
      
        element.innerHTML = html;
      </script>{/ignore}
      PAD,
      '<div id="ignore-example-4" data-users="[{&quot;id&quot;:1,&quot;name&quot;:&quot;Alice&quot;,&quot;role&quot;:&quot;Developer&quot;},{&quot;id&quot;:2,&quot;name&quot;:&quot;Bob&quot;,&quot;role&quot;:&quot;Designer&quot;},{&quot;id&quot;:3,&quot;name&quot;:&quot;Charlie&quot;,&quot;role&quot;:&quot;Manager&quot;}]"></div><script>const element = document.getElementById(\'ignore-example-4\');const users = JSON.parse(element.dataset.users);const html = \'<ul>\' + users.map(user =>\'<li><strong>\' + user.name + \'</strong> - \' + user.role + \'</li>\').join(\'\') + \'</ul>\';element.innerHTML = html;</script>',
      [
        'userData' => [
          [
            'id' => 1,
            'name' => 'Alice',
            'role' => 'Developer'
          ],
          [
            'id' => 2,
            'name' => 'Bob',
            'role' => 'Designer'
          ],
          [
            'id' => 3,
            'name' => 'Charlie',
            'role' => 'Manager'
          ]
        ],
        'usersJson' => '[{&quot;id&quot;:1,&quot;name&quot;:&quot;Alice&quot;,&quot;role&quot;:&quot;Developer&quot;},{&quot;id&quot;:2,&quot;name&quot;:&quot;Bob&quot;,&quot;role&quot;:&quot;Designer&quot;},{&quot;id&quot;:3,&quot;name&quot;:&quot;Charlie&quot;,&quot;role&quot;:&quot;Manager&quot;}]'
      ] ],

    [ 'fragments/ignore_5',
      <<<'PAD'
      <pre>{data 'rawJson' ignore}
      {
        "theme": "dark",
        "settings": {
          "notifications": true
        }
      }
      {/data}</pre>
      
      <p>The JSON curly braces were not parsed as PAD tags thanks to <code>ignore</code>.</p>
      PAD,
      '<pre></pre><p>The JSON curly braces were not parsed as PAD tags thanks to <code>ignore</code>.</p>' ],

    [ 'fragments/pipes_1',
      <<<'PAD'
      <p>Original: {$name}</p>
      <p>Uppercase: {echo $name | upper}</p>
      <p>Capitalize: {echo $name | ucwords}</p>
      
      <p>Original: {$email}</p>
      <p>Lowercase: {echo $email | lower}</p>
      
      <p>Original: {$price}</p>
      <p>Formatted: {echo $price | number_format(@, 2)}</p>
      PAD,
      '<p>Original: john doe</p><p>Uppercase: JOHN DOE</p><p>Capitalize: John Doe</p><p>Original: ALICE@EXAMPLE.COM</p><p>Lowercase: alice@example.com</p><p>Original: 1234.56</p><p>Formatted: 1,234.56</p>',
      [
        'name' => 'john doe',
        'email' => 'ALICE@EXAMPLE.COM',
        'price' => 1234.56
      ] ],

    [ 'fragments/pipes_2',
      <<<'PAD'
      <p>Items separated by commas:</p>
      <p>{items print, glue=", "}</p>
      
      <p>Items in uppercase:</p>
      <ul>
        {items}
          <li>{$items}</li>
        {/items | upper}
      </ul>
      PAD,
      '<p>Items separated by commas:</p><p>apple, banana, cherry</p><p>Items in uppercase:</p><ul><LI>APPLE</LI><LI>BANANA</LI><LI>CHERRY</LI></ul>',
      [
        'items' => [
          'apple',
          'banana',
          'cherry'
        ]
      ] ],

    [ 'fragments/pipes_3',
      <<<'PAD'
      <p>Chain multiple pipes together:</p>
      
      <p>Original: [{$rawText}]</p>
      <p>Trimmed and uppercase: [{echo $rawText | trim | upper}]</p>
      
      <p>Original: {$message}</p>
      <p>Uppercase first letter of each word: {echo $message | ucwords}</p>
      <p>Replace and uppercase: {echo $message | replace('fox', 'dog') | ucwords}</p>
      PAD,
      '<p>Chain multiple pipes together:</p><p>Original: [  hello world  ]</p><p>Trimmed and uppercase: [HELLO WORLD]</p><p>Original: the quick brown fox</p><p>Uppercase first letter of each word: The Quick Brown Fox</p><p>Replace and uppercase: The Quick Brown Dog</p>',
      [
        'rawText' => '  hello world  ',
        'message' => 'the quick brown fox'
      ] ],

    // The fragment's data is a list on purpose: bare text in a {data} pair comes back as no
    // occurrences at all, and the difference between the two pipes only shows over rows.

    [ 'fragments/pipes_4',
      <<<'PAD'
      {data 'words'}("alpha","beta"){/data}

      <p>Closing tag pipe - handed everything the tag rendered: [{words}{$words} {/words | upper}]</p>
      <p>Opening tag pipe - handed the content template, which then repeats: [{words | upper}row {/words}]</p>
      PAD,
      '<p>Closing tag pipe - handed everything the tag rendered: [ALPHA BETA ]</p>'
      . '<p>Opening tag pipe - handed the content template, which then repeats: [ROW ROW ]</p>' ],

  ];

?>
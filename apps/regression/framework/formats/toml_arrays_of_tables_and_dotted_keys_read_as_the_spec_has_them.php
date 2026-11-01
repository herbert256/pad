<?php

  // The arrays of tables, the dotted keys and the keys of the TOML 1.0 specification: an
  // empty [[products]] is an empty table in its place, [fruits.physical] belongs to the last
  // fruit and [[fruits.varieties]] nests, dotted keys make tables a [fruit.apple.texture]
  // header may add a sub-table to, 3.14159 is two keys, a quoted key holds its dots.

  $tomlTables = padFormatRead ( 'toml', <<<'TOML'
"127.0.0.1" = "value"
site."google.com" = true
3.14159 = "pi"
animal = { type.name = "pug" }
contributors = [
  "Foo Bar <foo@example.com>",
  { name = "Baz Qux", email = "bazqux@example.com" }, # a comment between the values
]

[fruit]
apple.color = "red"
apple.taste.sweet = true

[fruit.apple.texture]
smooth = true

[[products]]
name = "Hammer"
sku = 738594937

[[products]]  # empty table within the array

[[products]]
name = "Nail"
sku = 284758393
color = "gray"

[[fruits]]
name = "apple"

[fruits.physical]
color = "red"

[[fruits.varieties]]
name = "red delicious"

[[fruits.varieties]]
name = "granny smith"

[[fruits]]
name = "banana"

[[fruits.varieties]]
name = "plantain"
TOML );

  $tomlJson = json_encode ( $tomlTables, JSON_UNESCAPED_SLASHES );

?>

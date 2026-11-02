# widgets: treeview

`{widget:treeview data='nodes', label='Documentation'/}` consumes flat records
`id`, `parent` (empty for root), `label`, `href`. Duplicate IDs and cycles fail;
unknown parents become roots, depth is bounded to 32 and node count to 2000.
Without script, nested links are all visible. Enhancement adds tree roles, one tab
stop, Up/Down/Home/End navigation, Right to expand/enter, Left to collapse/leave,
Space to select and Enter to follow the native link. Selection emits `pad-tree-select`
with `detail.id`; applications own routing and persistent selection.

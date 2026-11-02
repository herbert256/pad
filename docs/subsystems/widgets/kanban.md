# widgets: kanban

`{widget:kanban data='cards', columns=$columns, label='Release board'/}` consumes
id/title/text/status records and an ordered status-to-label column map. Defaults are
todo/doing/done. Cards remain readable without scripting. Keyboard users move cards
with the native Move to select; pointer users can drag within the same board.
`pad-kanban-beforemove` is cancelable (permission/application validation);
`pad-kanban-move` supplies id/from/to for application persistence. No server request
or permission grant is implied. Labels escape records, unknown columns and duplicate
IDs fail. At most 12 columns and 1000 cards are rendered.

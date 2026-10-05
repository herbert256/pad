; nvim-treesitter-textobjects: a pair of tags is a block - "inside this {items} loop" is
; @block.inner - a tag is a call with its arguments, and comments are comments.

(block
  (body) @block.inner) @block.outer

(ignore_block
  (raw_text) @block.inner) @block.outer

[
  (tag)
  (field_tag)
] @call.outer

(arguments) @call.inner

(arguments
  (_) @parameter.inner)

(comment) @comment.outer

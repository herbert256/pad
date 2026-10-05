; Helix (and Zed's vim mode): a pair of tags is the "function" text object - mif selects
; inside this {items} loop, maf the loop with its tags - the arguments of a tag are its
; parameters, and comments are comments.

(block
  (body) @function.inside) @function.around

(ignore_block
  (raw_text) @function.inside) @function.around

(arguments
  (_) @parameter.inside)

(comment) @comment.inside

(comment) @comment.around

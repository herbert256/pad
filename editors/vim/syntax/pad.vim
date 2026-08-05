" Vim syntax file for PAD templates (*.pad)
" HTML base with PAD constructs on top.

if exists('b:current_syntax')
  finish
endif

" Load HTML (brings in JS/CSS regions too)
runtime! syntax/html.vim
unlet! b:current_syntax

" {-- comment --}
syn region padComment start=/{--/ end=/--}/ containedin=ALLBUT,padComment,padStringS,padStringD keepend

" @page@ etc.
syn match padConstruct /@\(page\|content\|start\|end\|else\|tidy\)@/ containedin=ALLBUT,padComment,padStringS,padStringD

" { ... } PAD tag - injected into html strings, tags, script and style too
syn region padTag matchgroup=padBrace start=/{\ze[\/A-Za-z_$%!@]/ end=/}/
      \ containedin=ALLBUT,padComment,padStringS,padStringD
      \ contains=padTagName,padPrefix,padStringS,padStringD,padVarSigil,padVarName,padProperty,padPlaceholder,padOperator,padNumber,padParm,padPipe,padTag
      \ keepend extend

" tag name right after the brace (optional /, optional prefix:)
syn match padTagName /\({\/\?\)\@<=[A-Za-z_][A-Za-z0-9_]*/ contained
syn match padPrefix  /\({\/\?\)\@<=[A-Za-z_][A-Za-z0-9_]*:/he=e-1 contained nextgroup=padTagName

" variables: red sigil, yellow-ish name
syn match padVarSigil /[$%]\ze[A-Za-z_]/ contained
syn match padVarName  /\([$%]\)\@<=[A-Za-z_][A-Za-z0-9_.]*\(\[[^]]*\]\)\?/ contained

" first@items etc.
syn match padProperty /\<[A-Za-z_][A-Za-z0-9_]*@[A-Za-z_][A-Za-z0-9_]*\>/ contained

" the @ placeholder
syn match padPlaceholder /@/ contained

" operators
syn keyword padOperator eq ne gt lt ge le and or xor not range contained
syn match padOperator "\*\*\|==\|!=\|<>\|<=\|>=\|[<>+*/%.|-]" contained

" numbers, parameters, pipe
syn match padNumber /\<\d\+\(\.\d\+\)\?\>/ contained
syn match padParm /\<[A-Za-z_][A-Za-z0-9_]*\ze\s*=[^=>]/ contained
syn match padPipe /|/ contained

" strings inside tags
syn region padStringS start=/'/ skip=/\\'/ end=/'/ contained
syn region padStringD start=/"/ skip=/\\"/ end=/"/ contained

hi def link padComment     Comment
hi def link padConstruct   PreProc
hi def link padBrace       Delimiter
hi def link padTagName     Statement
hi def link padPrefix      Type
hi def link padVarSigil    Special
hi def link padVarName     Identifier
hi def link padProperty    Function
hi def link padPlaceholder Special
hi def link padOperator    Operator
hi def link padNumber      Number
hi def link padParm        Type
hi def link padPipe        Operator
hi def link padStringS     String
hi def link padStringD     String

let b:current_syntax = 'pad'

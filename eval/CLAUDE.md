# eval/

Expression evaluation engine for parsing and executing expressions within `{...}`.

## Main Files
- `eval.php` - Main evaluation entry point
- `fast.php` - Fast-path for simple function calls

## Subdirectories
- `actions/` - Evaluation action handlers
- `go/` - Evaluation execution handlers
- `parms/` - Parameter parsing (app.php, tag.php)
- `single/` - Single-value evaluation (property.php, tag.php)
- `type/` - Type-specific evaluation (type.php)

## Evaluation Flow
1. `padEvalParse()` - Tokenizes the expression
2. `padEvalAfter()` - Post-processing (resolve types, operators, values)
3. `padEvalPipes()` - Splits into pipe stages
4. `padEvalResult()` - Executes each stage

## Expression Syntax
- `{$field}` - Field/variable value
- `{&tag}` - Tag value
- `{#opt}` - Option value
- `{value|function1|function2}` - Pipe chain
- Operators: arithmetic, comparison, logical (AND, OR, NOT, etc.)

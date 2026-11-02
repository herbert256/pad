# inputs: repeatable

`{input:repeatable 'lines', columns=array:columns, value=array:lines, minrows=1, maxrows=20, label='Invoice lines'/}`
uses real names such as `lines[0][quantity]`. `columns` maps safe column identifiers
to labels (up to 12); up to 100 rows. Native text controls render on the server;
without JavaScript existing rows remain editable and submit normally. Enhancement adds
keyboard-accessible add/remove buttons; indices are never recycled while mounted.
On the server use `padValidateRows('lines', ['quantity'=>'required|numeric'], $_POST)`;
it expands actual nested addresses and rejects malformed/excess rows. PAD retains messages automatically and shows them beside the exact bracketed control.
The application owns field-specific validation, totals and persistence. A disabled
field disables its rows and marker together. For advanced per-column input widgets,
use explicit application fields rather than assuming arbitrary templates are cloned.

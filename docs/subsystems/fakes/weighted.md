# Fake: weighted

`padFake('weighted', ['values' => ['open', 'closed'], 'weights' => [1, 9]])` selects proportionally to weights using PAD's seeded stream. Arrays are paired in iteration order; values retain their PHP types. Zero weights never win; negative, non-finite, mismatched or all-zero weights fail. At most 10000 choices. Weights are normalized to avoid overflow.

Template equivalent: `{fake:weighted/}`, with the same named options. Errors call `padError` and return NULL if execution continues. Run `php tests/subsystems/fake-weighted.php`.

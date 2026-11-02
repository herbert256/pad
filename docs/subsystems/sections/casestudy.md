# sections: casestudy

`{section:casestudy title='Results', data='cases'/}` accepts title, problem, approach,
result, metrics, image, alt and attribution fields. Nested `section:item` uses the
first argument as title and its body as result; other fields are named options.
Each case is an article with a definition list, optional metric summary, image and
attribution. Data values are escaped; authored nested content follows normal section
HTML rules. Supply descriptive image alt text. No metrics are calculated or invented.

# karadiff
A diff utility especially friendly to music lyrics

## Setup
After cloning, launch from the public folder. Tested against PHP built-in server.

## Tests
For the moment, the test suite covers the internals of the core diffing library. 
More unit tests are needed for diff providers and renderers.
There's no functional test coverage yet.

## Personal notes
The design of the app is intended to be demonstrative. Everything is written from the ground up combining components instead of relying on a single framework. The design is simplicistic but i tried to expose a number of concepts.

## Sources
The diff algorithm is inspired by GNU's `diff` and the renderer used in the webapp implements GNU's `wdiff` basic idea.
The algorithm is called after its creator, Douglas McIlroy.
This implementation for PHP is partially ported from Tim Peters' implementation as found in the original `difflib` Python library.

## Current output
At the moment, the webapps crudely outputs diffs like:

`-word` (removal) `+word` (addition) ` ` (common text).

On the left side are displayed the removals, on the right one the additions.

## Todo
Among the other things, find a way to control coloring and highlighting for the output in CodeMirror's boxes.

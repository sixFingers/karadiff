# karadiff
A diff utility especially friendly to music lyrics

## Setup
After cloning, launch from the public folder. Tested against PHP built-in server.

## Tests
For the moment, the test suite covers the internals of the core diffing library. 
More unit tests are needed for diff providers and renderers.
There's no functional test coverage yet.

## Sources
The diff algorithm is inspired by GNU's ´diff´ and the renderer used in the webapp implements GNU's `wdiff` basic idea.
The algorithm is called after its creator, Douglas McIlroy.
This implementation for PHP is partially ported from Tim Peters' implementation in the original `difflib` library.

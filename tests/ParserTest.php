<?php

class ParserTest extends \PHPUnit_Framework_TestCase {

    public function testStrSuccesses() {
      $this->assertTrue(
        Parser::str('abc')->run('abcdef')->equal(new Good('abc', 3))
      );

      $this->assertEquals(
        Parser::product(
          Parser::str('ab'),
          Parser::str('cd')
        )->run('abcd'),
        new Good(array('ab', 'cd'), 4),
        'Parsing product'
      );

      $this->assertEquals(
        Parser::str('abc')->chain(function ($s) {
          return Parser::str(strtoupper($s));
        })->run('abcABC'), new Good('ABC', 6),
        'Contextual parsing'
      );

      $this->assertEquals(
        Parser::times(3, Parser::str('ab'))->run('ababab'),
        new Good(array('ab', 'ab', 'ab'), 6),
        'Parser::times(n, p) when successful must produce list of n results.'
      );

      $count = function ($xs) { return count($xs); };

      $this->assertEquals(
        Parser::times(3, Parser::str('ab'))->map($count)->run('ababab'),
        new Good(3, 6),
        'Parser::times(n, p)->map(count) when successful must produce n.'
      );
    }

    public function testRegex() {
      $this->assertEquals(
        Parser::regex('/^a(\w)c$/')->run('abc'),
        new Good(array('abc', 'b'), 3),
        'Parser::regex(pattern) when successful must produce an array of matches.'
      );
    }

    public function testTextParsing() {
      $this->assertEquals(
        Parser::satisfyStr(function ($s) { return $s === 'ab'; })->run('ab'),
        new Good('ab', 2),
        'Parser::satisfyStr(f) when successful must produce the input.'
      );

      $this->assertEquals(
        Parser::anyChar()->run('a'),
        new Good('a', 1),
        'Parser::anyChar() when successfully run with a char c must produce c.'
      );

      $this->assertEquals(
        new Good('a', 1),
        Parser::anyChar()->run('abc'),
        'Parser::anyChar() when successfully run with a char s must produce (s minus first char).'
      );

      $this->assertEquals(
        Parser::anyChar()->run(''),
        new Bad((new Location('', 0))->toError("Did not satisfy predicate with input ''")),
        'Parser::anyChar() fails when run on an empty string.'
      );

    }

    public function testChain() {
      $this->assertEquals(
        new Good(array('A', 'A'), 3),
        Parser::anyChar()->chain(function ($c) {
          return Parser::times(2, Parser::char(strtoupper($c)));
        })->run('aAA'),
        'Parser::chain() must pass context and update character consumption status appropriately.'
      );
    }

}


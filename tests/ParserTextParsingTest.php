<?php

class ParserTextParsingTest extends \PHPUnit_Framework_TestCase {

    public function testStrSuccesses() {
      $this->assertEquals(
        new Good('abc', 3),
        Parser::str('abc')->run('abcdef')
      );

      $this->assertEquals(
        new Good(array('ab', 'cd'), 4),
        Parser::product(
          Parser::str('ab'),
          Parser::str('cd')
        )->run('abcd'),
        'Parser::product() of two successful parsers is an array of each respective result.'
      );

      $this->assertEquals(
        new Good('ABC', 6),
        Parser::str('abc')->chain(function ($s) {
          return Parser::str(strtoupper($s));
        })->run('abcABC'),
        'Parser::chain() allows context-sensitive parsing and the character consumption is correctly managed.'
      );

      $this->assertEquals(
        new Good(array('ab', 'ab', 'ab'), 6),
        Parser::times(3, Parser::str('ab'))->run('ababab'),
        'Parser::times(n, p) when successful must produce list of n results.'
      );

      $count = function ($xs) { return count($xs); };

      $this->assertEquals(
        new Good(3, 6),
        Parser::times(3, Parser::str('ab'))->map($count)->run('ababab'),
        'Parser::times(n, p)->map(count) when successful must produce n.'
      );
    }

    public function testRegex() {
      $this->assertEquals(
        new Good(array('abc', 'b'), 3),
        Parser::regex('/^a(\w)c$/')->run('abc'),
        'Parser::regex(pattern) when successful must produce an array of matches.'
      );
    }

    public function testSatisfyStr() {
      $this->assertEquals(
        new Good('ab', 2),
        Parser::satisfyStr(function ($s) { return $s === 'ab'; })->run('ab'),
        'Parser::satisfyStr(f) when successful must produce the input.'
      );
    }

    public function testAnyChar() {
      $this->assertEquals(
        new Good('a', 1),
        Parser::anyChar()->run('a'),
        'Parser::anyChar() when successfully run with a char c must produce c.'
      );

      $this->assertEquals(
        new Good('a', 1),
        Parser::anyChar()->run('abc'),
        'Parser::anyChar() when successfully run on a string s must produce the matched char.'
      );

      $this->assertEquals(
        new Bad((new Location('', 0))->toError("Did not satisfy predicate with input ''"), new Uncommitted),
        Parser::anyChar()->run(''),
        'Parser::anyChar() fails when run on an empty string.'
      );
    }

    public function testAnyDigit() {
      $this->assertEquals(
        new Good('3', 1),
        Parser::anyDigit()->run('3'),
        'Parser::anyDigit() when successfully run on a digit d must produce d.'
      );
    }

}

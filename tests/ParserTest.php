<?php

class ParserTest extends \PHPUnit_Framework_TestCase {

    public function testAlternative() {
      $ab = Parser::str('ab');
      $cd = Parser::str('cd');

      $this->assertEquals(
        new Good('ab', 2),
        $ab->or_($cd)->run('ab'),
        'Parser::or_()'
      );

      $this->assertEquals(
        new Good('ab', 2),
        $cd->or_($ab)->run('ab'),
        'Parser::or_()'
      );
    }

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
        'Parser::anyChar() when successfully run on a char s must produce (s minus first char).'
      );

      $this->assertEquals(
        Parser::anyChar()->run(''),
        new Bad((new Location('', 0))->toError("Did not satisfy predicate with input ''")),
        'Parser::anyChar() fails when run on an empty string.'
      );

      $this->assertEquals(
        new Good('3', 1),
        Parser::anyDigit()->run('3'),
        'Parser::anyDigit() when successfully run on a digit d must produce d.'
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

      $this->assertEquals(
        new Good(array('A', 'A', 'A'), 5),
        Parser::product(Parser::anyDigit(), Parser::anyChar())->chain(function ($x) {
          return Parser::times(intval($x[0]), Parser::char(strtoupper($x[1])));
        })->run('3aAAA'),
        'Parser::chain() must pass context and update character consumption status appropriately.'
      );
    }

    public function testSlice() {
      $this->assertEquals(
        new Good('abcd', 4),
        Parser::slice(
          Parser::product(
            Parser::str('ab'),
            Parser::str('cd')
          )
        )->run('abcd'),
        'Parser::slice() when successful produces the inspected input string.'
      );

      $this->assertEquals(
        new Good(array('ab', 'cdef'), 6),
        Parser::product(
          Parser::str('ab'),
          Parser::slice(
            Parser::product(
              Parser::str('cd'),
              Parser::str('ef')
            )
          )
        )->run('abcdef'),
        'Parser::slice() when successful produces the inspected input string.'
      );
    }

    public function testMany() {
      $this->assertEquals(
        new Good(array('a', 'a', 'a'), 3),
        Parser::many(Parser::char('a'))->run('aaa'),
        'Parser::many() when successful produces an array for every match'
      );

      $this->assertEquals(
        new Good(array(), 0),
        Parser::many(Parser::char('a'))->run('bbb'),
        'Parser::many() produces empty array when no matches'
      );

      $spaces = Parser::many(Parser::char(' '));
      $fst = function (array $x) { return $x[0]; };
      $this->assertEquals(
        new Good(array('a', 'b'), 5),
        Parser::product(
          Parser::product(Parser::char('a'), $spaces)->map($fst),
          Parser::char('b')
        )->run('a   b'),
        ''
      );
    }

    public function testMany1() {
      $this->assertEquals(
        new Good(array('a', 'a', 'a'), 3),
        Parser::many1(Parser::char('a'))->run('aaa'),
        'Parser::many1() when successful produces an array for every match'
      );

      $this->assertEquals(
        new Bad((new Location('baaa', 0))->toError("Expected the string 'a'")),
        Parser::many1(Parser::char('a'))->run('baaa'),
        'Parser::many1() fails when not even one success'
      );
    }

    public function testSkipping() {
      $this->assertEquals(
        new Good('b', 2),
        Parser::skipL(Parser::char('a'), Parser::char('b'))->run('ab'),
        'Parser::skipL(p1,p2) discards result of p1'
      );
    }

}

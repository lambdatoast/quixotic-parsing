<?php

class ParserTest extends \PHPUnit_Framework_TestCase {

    public function testAlternative() {
      $ab = Parser::str('ab');
      $cd = Parser::str('cd');
      $ad = Parser::str('ad');

      $this->assertEquals(
        new Good('ab', 2),
        $ab->or_($cd)->run('ab'),
        'Parser::or_(p, q) returns p when p is successful'
      );

      $this->assertEquals(
        new Good('ab', 2),
        $cd->or_($ab)->run('ab'),
        'Parser::or_(p, q) returns q when p fails uncommitted, and q is successful'
      );

      $this->assertEquals(
        new Bad((new Location('ab', 1))->toError("Expected the string 'ad'"), new Committed),
        $ad->or_($ab)->run('ab'),
        'Parser::or_(p, q) fails when p fails committed, regardless of what q is'
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
        new Bad((new Location('baaa', 0))->toError("Expected the string 'a'"), new Uncommitted),
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

      $this->assertEquals(
        new Good('a', 2),
        Parser::skipR(Parser::char('a'), Parser::char('b'))->run('ab'),
        'Parser::skipR(p1,p2) discards result of p2'
      );
    }

    public function testChainl1() {
      $int = Parser::satisfyChar(function ($c) { return is_numeric($c); })->map(function ($c) { return intval($c); });
      $addop = Parser::char('+')->map(function ($_) { return function ($x, $y) { return $x + $y; }; });

      $this->assertEquals(
        new Good(15, 9),
        Parser::chainl1($int, $addop)->run('1+2+3+4+5'),
        'Parser::chainl1(p, op) collapses `p`s that are separated by `op`'
      );

      $this->assertEquals(
        new Good(1, 1),
        Parser::chainl1($int, $addop)->run('1'),
        'Parser::chainl1(p, op) is successful when input only has enough for a `p` success'
      );


    }

}

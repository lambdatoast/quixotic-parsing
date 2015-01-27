<?php

interface Combinator extends Alternative, Chain, Functor {

  /**
   * Parser that always succeeds with value $a.
   * @param mixed $a.
   * @return Combinator
   */
  static function succeed($a);

  /**
   * @param Combinator $p1
   * @param Combinator $p2
   * $param callable $f Takes two results and returns something.
   * @return Combinator
   */
  static function map2(Combinator $p1, Combinator $p2, callable $f);

  /**
   * @param Combinator $p1
   * @param Combinator $p2
   * @return Combinator Combinator whose running Result is tuple of the two parsers.
   */
  static function product(Combinator $p1, Combinator $p2);

  /**
   * @param int $n
   * @param Combinator $p
   * @return Combinator Combinator whose running Result is array of $n values.
   */
  static function times($n, Combinator $p);

  /**
   * @param Combinator $p
   * @return Combinator Combinator whose success Result is the string consumed by $p.
   */
  static function slice(Combinator $p);

  /**
   * @param Combinator $p
   * @return Combinator Combinator whose success Result is an array whose length equals the number of matches.
   */
  static function many(Combinator $p);

  /**
   * @param Combinator $p
   * @return Combinator Combinator whose success Result is an array whose length equals the number of matches.
   */
  static function many1(Combinator $p);

  /**
   * Run the two parsers and ignore the value of the left one.
   * @param Combinator $p1
   * @param Combinator $p2
   * @return Combinator
   */
  static function skipL(Combinator $p1, Combinator $p2);

  /**
   * Run the two parsers and ignore the value of the right one.
   * @param Combinator $p1
   * @param Combinator $p2
   * @return Combinator
   */
  static function skipR(Combinator $p1, Combinator $p2);

  /**
   * @param String $input
   * @return Result
   */
  function run($input);

}

<?php

interface Parsing extends Alternative, Chain, Functor {

  /**
   * Parser that always succeeds with value $a.
   * @param mixed $a.
   * @return Parsing
   */
  static function succeed($a);

  /**
   * @param string $a Should be a single character.
   * @return Parsing
   */
  static function char($c);

  /**
   * @param string $s
   * @return Parsing
   */
  static function str($s);

  /**
   * @param string $regex
   * @return Parsing
   */
  static function regex($pattern);

  /**
   * @param Parsing $p1
   * @param Parsing $p2
   * $param callable $f Takes two results and returns something.
   * @return Parsing
   */
  static function map2(Parsing $p1, Parsing $p2, callable $f);

  /**
   * @param Parsing $p1
   * @param Parsing $p2
   * @return Parsing Parsing whose running Result is tuple of the two parsers.
   */
  static function product(Parsing $p1, Parsing $p2);

  /**
   * @param int $n
   * @param Parsing $p
   * @return Parsing Parsing whose running Result is array of $n values.
   */
  static function times($n, Parsing $p);

  /**
   * @param String $input
   * @return Result
   */
  function run($input);

}

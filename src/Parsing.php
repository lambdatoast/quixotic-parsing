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
   * @param Parsing $p1
   * @param Parsing $p2
   * $param callable $f Takes two results and returns something.
   * @return Parsing
   */
  static function map2(Parser $p1, Parser $p2, callable $f);

  /**
   * @param Parsing $p1
   * @param Parsing $p2
   * @return Parsing Parsing whose running Result is tuple of the two parsers.
   */
  static function product(Parser $p1, Parser $p2);

  /**
   * @param int $n
   * @param Parsing $p
   * @return Parsing Parsing whose running Result is array of $n values.
   */
  static function times($n, Parser $p);

  /**
   * @param String $input
   * @return Result
   */
  function run($input);

}


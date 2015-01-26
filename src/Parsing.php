<?php

abstract class Parsing implements Alternative, Chain, Functor {
  /**
   * Combinators.
   */

  // A -> Parser[A]
  abstract static function succeed($a);
  // Char -> Parser[Char]
  abstract static function char($c);
  // String -> Parser[String]
  abstract static function str($s);
  abstract static function map2(Parser $a, Parser $b, callable $f);
  abstract static function product(Parser $a, Parser $b);
  // String -> Result[A]
  abstract function run($input);

  // Functor instance

  function map(callable $f) { 
    return $this->chain(function ($a) use ($f) {
      return $this::succeed($f($a));
    });
  }
}


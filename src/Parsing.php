<?php

interface Parsing extends Alternative, Chain, Functor {

  // A -> Parser[A]
  static function succeed($a);
  // Char -> Parser[Char]
  static function char($c);
  // String -> Parser[String]
  static function str($s);
  static function map2(Parser $a, Parser $b, callable $f);
  static function product(Parser $a, Parser $b);
  // String -> Result[A]
  function run($input);

}


<?php

class ParserError {
  var $errors = array();

  function __construct($errors) {
    $this->errors = $errors;
  }

  function latest() {
    $copy = array_merge($this->errors, array());
    return array_pop($copy);
  }

}



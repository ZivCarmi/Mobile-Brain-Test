<?php

/**
 *
 * Returns the last input value of a field
 *
 * @param string  $input_name the field name
 * @return string
 *
 */
if (!function_exists('old')) {
  function old($input_name)
  {
    return $_REQUEST[$input_name] ?? '';
  }
}

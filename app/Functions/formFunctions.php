<?php

function csrf()
{
  $_SESSION['_token'] = bin2hex(random_bytes(32));
  return '<input type="hidden" name="_token" value="' . $_SESSION['_token'] . '">';
}

function method($method)
{
  return '<input type="hidden" name="_method" value="' . $method . '">';
}
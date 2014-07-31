<?php

use PhpTry\Result;

require_once __DIR__ . '/../vendor/autoload.php';

function divide($dividend, $divisor) {
    if ( ! is_numeric($dividend) || ! is_numeric($divisor)) {
        throw new InvalidArgumentException('Divident and diviser should be numeric.');
    }

    if ($divisor == 0) {
        throw new InvalidArgumentException('Divisor cannot be 0.');
    }

    return $dividend / $divisor;
}

function multiply($a, $b) {
    if ( ! is_numeric($a) || ! is_numeric($b)) {
        throw new InvalidArgumentException('Can only multiply numeric values.');
    }

    return $a * $b;
}

function prompt($text) {
    echo $text . "\n";

    return trim(fgets(STDIN));

}

function promptDivide() {
    $a = prompt("Enter a number (a) that you'd like to divide:");
    $b = prompt("Enter a number (b) that you'd like to divide by:");
    $c = prompt("Enter a number (c) that you'd like to multiply by:");

    return Attempt::call('divide', array($a, $b))
        ->map(function($elem) use ($c) { return multiply($elem, $c); });
}

$try = promptDivide();

// on* handlers
$try
    ->onSuccess(function($elem) { echo "Result of a / b * c is: $elem\n"; })
    ->onFailure(function($elem) { echo "Something went wrong: " . $elem->getMessage() . "\n"; promptDivide(); })
;

//// get() the value, will throw
//echo $try->get();
//
//// return a default value if Failure
//echo $try->getOrElse(-1);
//
//// return a default value returned by a function if Failure
//echo $try->getOrCall(function() { return -1; });
//
//// or else return another attempt
//echo $try->orElse(Attempt::call('divide', array(42, 21)))->get();
//
//// or else return another attempt from a callable
//echo $try->orElseCall('promptDivide')->get();
//
//// only foreachable if success
//foreach ($try as $result) {
//    echo $result;
//}
//
//// map with another attempt
//echo $try->flatMap(function($elem) {
//    return Attempt::call('divide', array($elem, promptDivide()->get()));
//})->get();
//
//// map the success value to another value
//echo $try->map(function($elem) { return $elem * 2; })->get();
//
//// filter the success value, returns Failure if it doesn't match the filter
//echo $try->filter(function($elem) { return $elem === 42; })->get();
//
//// recover with with a value returned by a callable
//echo $try
//  ->filter(function($elem) { return $elem === 42; })
//  ->recover(function($ex) { if ($ex instanceof RuntimeException) { return 21; } throw $ex; })
//  ->get();
//
//// recover with with an attempt returned by a callable
//echo $try
//  ->filter(function($elem) { return $elem === 42; })
//  ->recoverWith(function() { return promptDivide(); })
//  ->get();

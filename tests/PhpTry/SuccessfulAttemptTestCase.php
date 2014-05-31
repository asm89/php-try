<?php

namespace PhpTry;

use Exception;
use PHPUnit_Framework_TestCase;

abstract class SuccessfulAttemptTestCase extends PHPUnit_Framework_TestCase
{
    protected $success;

    public function setUp()
    {
        $this->success = $this->createSuccess(42);
    }

    abstract protected function createSuccess($value);

    /**
     * @test
     */
    public function it_is_success()
    {
        $this->assertTrue($this->success->isSuccess());
    }

    /**
     * @test
     */
    public function it_is_not_failure()
    {
        $this->assertFalse($this->success->isFailure());
    }

    /**
     * @test
     */
    public function it_returns_the_wrapped_value()
    {
        $this->assertEquals(42, $this->success->get());
    }

    /**
     * @test
     */
    public function it_returns_the_wrapped_value_on_getOrElse()
    {
        $this->assertEquals(42, $this->success->getOrElse(21));

    }

    /**
     * @test
     */
    public function it_returns_the_wrapped_value_on_getOrCall()
    {
        $this->assertEquals(42, $this->success->getOrCall(function(){ return 21; }));
    }

    /**
     * @test
     */
    public function it_returns_the_element_with_foreach()
    {
        $called = 0;
        foreach ($this->success as $elem) {
            $this->assertEquals(42, $elem);
            $called++;
        }

        $this->assertEquals(1, $called);
    }

    /**
     * @test
     * @dataProvider attemptValues
     */
    public function it_returns_the_result_of_the_callable_on_flatMap(Attempt $attempt)
    {
        $this->assertSame($attempt, $this->success->flatMap(function() use ($attempt) { return $attempt; }));
    }

    public function attemptValues()
    {
        return array(
            array(new Success(42)),
            array(new Failure(new Exception())),
        );
    }

    /**
     * @test
     */
    public function it_passes_its_value_to_the_flatMap_callable()
    {
        $value = null;

        $this->success->flatMap(function($elem) use (&$value) { $value = $elem; return new Success(21); });

        $this->assertEquals($this->success->get(), $value);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_flatMap_callable_throws()
    {
        $result = $this->success->flatMap(function() { throw new Exception('meh.'); });

        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_flatMap_callable_does_not_return_an_Attempt()
    {
        $result = $this->success->flatMap(function() { return 21; });

        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_returns_the_result_of_the_callable_on_map()
    {
        $this->assertEquals(new Success(21), $this->success->map(function() { return 21; }));
    }

    /**
     * @test
     */
    public function it_passes_its_value_to_the_map_callable()
    {
        $value = null;

        $this->success->map(function($elem) use (&$value) { $value = $elem; return 21; });

        $this->assertEquals($this->success->get(), $value);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_map_callable_throws()
    {
        $result = $this->success->map(function() { throw new Exception('meh.'); });

        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_callable_returns_false_on_filter()
    {
        $result = $this->success->filter(function() { return false; });
        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_passes_its_value_to_the_filter_callable()
    {
        $value = null;

        $this->success->filter(function($elem) use (&$value) { $value = $elem; return true; });

        $this->assertEquals($this->success->get(), $value);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_filter_callable_throws()
    {
        $result = $this->success->filter(function() { throw new Exception('meh.'); });

        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_passes_its_value_to_the_onSuccess_callable()
    {
        $value = null;

        $this->success->onSuccess(function($elem) use (&$value) { $value = $elem; });

        $this->assertEquals($this->success->get(), $value);
    }

    /**
     * @test
     */
    public function it_passes_does_not_call_onFailure()
    {
        $called = false;

        $this->success->onFailure(function() use (&$called) { $called = true; });

        $this->assertFalse($called);
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_a_Some_option()
    {
        $this->assertEquals(new \PhpOption\Some(42), $this->success->toOption());
    }

    /**
     * @test
     */
    public function it_calls_on_forAll()
    {
        $called = false;
        $value  = null;

        $this->success->forAll(function($v) use (&$called, &$value) { $called = true; $value = $v; });

        $this->assertTrue($called);
        $this->assertEquals(42, $value);
    }
}

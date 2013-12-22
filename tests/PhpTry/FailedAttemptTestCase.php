<?php

namespace PhpTry;

use Exception;
use PHPUnit_Framework_TestCase;

abstract class FailedAttemptTestCase extends PHPUnit_Framework_TestCase
{
    protected $exception;
    protected $failure;

    public function setUp()
    {
        $this->exception = new TestException();
        $this->failure   = $this->createFailure($this->exception);
    }

    abstract protected function createFailure($exception);

    /**
     * @test
     */
    public function it_is_not_success()
    {
        $this->assertFalse($this->failure->isSuccess());
    }

    /**
     * @test
     */
    public function it_is_failure()
    {
        $this->assertTrue($this->failure->isFailure());
    }

    /**
     * @test
     * @expectedException PhpTry\TestException
     */
    public function it_throws_when_getting_the_value()
    {
        $this->failure->get();
    }

    /**
     * @test
     */
    public function it_returns_the_other_value_on_getOrElse()
    {
        $this->assertEquals(21, $this->failure->getOrElse(21));

    }

    /**
     * @test
     */
    public function it_returns_the_value_returned_by_the_callable_on_getOrCall()
    {
        $this->assertEquals(21, $this->failure->getOrCall(function(){ return 21; }));
    }

    /**
     * @test
     * @expectedException PhpTry\TestException
     */
    public function it_returns_does_not_handle_the_exception_thrown_by_the_callable_on_getOrCall()
    {
        $this->failure->getOrCall(function(){ throw new TestException(); });
    }

    /**
     * @test
     */
    public function it_returns_the_other_attempt_on_orElse()
    {
        $other = new Success(42);
        $this->assertEquals($other, $this->failure->orElse($other));
    }

    /**
     * @test
     */
    public function it_returns_the_result_of_the_callable_on_orElseCall()
    {
        $this->assertEquals(new Success(21), $this->failure->orElseCall(function (){ return new Success(21); }));
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_result_of_the_callable_is_not_an_attempt_on_orElseCall()
    {
        $result = $this->failure->orElseCall(function (){ return 21; });
        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_callable_throws_on_orElseCall()
    {
        $result = $this->failure->orElseCall(function (){ throw new TestException(); });
        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_does_not_return_an_element_in_foreach()
    {
        $called = false;
        foreach ($this->failure as $elem) {
            $called = true;
        }

        $this->assertFalse($called);
    }

    /**
     * @test
     * @dataProvider attemptValues
     */
    public function it_returns_the_result_of_the_callable_on_recoverWith(Attempt $attempt)
    {
        $this->assertSame($attempt, $this->failure->recoverWith(function() use ($attempt) { return $attempt; }));
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
    public function it_passes_its_value_to_the_recoverWith_callable()
    {
        $value = null;

        $this->failure->recoverWith(function($elem) use (&$value) { $value = $elem; return new Success(21); });

        $this->assertSame($this->exception, $value);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_recoverWith_callable_throws()
    {
        $result = $this->failure->recoverWith(function() { throw new Exception('meh.'); });

        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_recoverWith_callable_does_not_return_an_Attempt()
    {
        $result = $this->failure->recoverWith(function() { return 21; });

        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_returns_the_result_of_the_callable_on_recover()
    {
        $this->assertEquals(new Success(21), $this->failure->recover(function() { return 21; }));
    }

    /**
     * @test
     */
    public function it_passes_its_value_to_the_recover_callable()
    {
        $value = null;

        $this->failure->recover(function($elem) use (&$value) { $value = $elem; return 21; });

        $this->assertSame($this->exception, $value);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_recover_callable_throws()
    {
        $result = $this->failure->recover(function() { throw new Exception('meh.'); });

        $this->assertInstanceOf('PhpTry\\Failure', $result);
    }

    /**
     * @test
     */
    public function it_passes_its_value_to_the_onFailure_callable()
    {
        $value = null;

        $this->failure->onFailure(function($elem) use (&$value) { $value = $elem; });

        $this->assertEquals($this->exception, $value);
    }

    /**
     * @test
     */
    public function it_passes_does_not_call_onSuccess()
    {
        $called = false;

        $this->failure->onSuccess(function() use (&$called) { $called = true; });

        $this->assertFalse($called);
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_a_None_option()
    {
        $this->assertEquals(\PhpOption\None::create(), $this->failure->toOption());
    }
}

class TestException extends Exception {}

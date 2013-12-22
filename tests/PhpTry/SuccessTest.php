<?php

namespace PhpTry;

use Exception;
use PHPUnit_Framework_TestCase;

class SuccessTest extends SuccessfulAttemptTestCase
{
    protected function createSuccess($value)
    {
        return new Success($value);
    }

    /**
     * @test
     */
    public function it_returns_itself_on_orElse()
    {
        $this->assertEquals($this->success, $this->success->orElse(new Failure(new Exception())));
    }

    /**
     * @test
     */
    public function it_returns_itself_on_orElseCall()
    {
        $this->assertEquals($this->success, $this->success->orElseCall(function (){ return 21; }));
    }

    /**
     * @test
     */
    public function it_returns_itself_if_the_callable_returns_true_on_filter()
    {
        $this->assertSame($this->success, $this->success->filter(function() { return true; }));
    }

    /**
     * @test
     */
    public function it_returns_itself_on_recoverWith()
    {
        $this->assertSame($this->success, $this->success->recoverWith(function(){}));
    }

    /**
     * @test
     */
    public function it_returns_itself_on_recover()
    {
        $this->assertSame($this->success, $this->success->recover(function(){}));
    }

    /**
     * @test
     */
    public function it_returns_itself_onSuccess()
    {
        $this->assertSame($this->success, $this->success->onSuccess(function() {}));
    }

    /**
     * @test
     */
    public function it_returns_itself_onFailure()
    {
        $this->assertSame($this->success, $this->success->onFailure(function() {}));
    }
}

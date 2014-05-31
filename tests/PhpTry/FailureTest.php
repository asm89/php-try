<?php

namespace PhpTry;

use Exception;
use PHPUnit_Framework_TestCase;

class FailureTest extends FailedAttemptTestCase
{
    protected function createFailure($exception)
    {
        return new Failure($this->exception);
    }

    /**
     * @test
     */
    public function it_returns_itself_on_flatMap()
    {
        $this->assertSame($this->failure, $this->failure->flatMap(function(){}));
    }

    /**
     * @test
     */
    public function it_returns_itself_on_map()
    {
        $this->assertSame($this->failure, $this->failure->map(function(){}));
    }

    /**
     * @test
     */
    public function it_returns_itself_on_filter()
    {
        $this->assertSame($this->failure, $this->failure->filter(function(){}));
    }

    /**
     * @test
     */
    public function it_returns_itself_onFailure()
    {
        $this->assertSame($this->failure, $this->failure->onFailure(function() {}));
    }

    /**
     * @test
     */
    public function it_returns_itself_onSuccess()
    {
        $this->assertSame($this->failure, $this->failure->onSuccess(function() {}));
    }

    /**
     * @test
     */
    public function it_returns_itself_forAll()
    {
        $this->assertSame($this->failure, $this->failure->forAll(function() {}));
    }
}

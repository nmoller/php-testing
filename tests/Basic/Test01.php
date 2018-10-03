<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Uqam\Basic\Test;

final class BasicTest extends TestCase
{
	protected function setUp()
    {
        $this->testInstance = new Test();
    }

    public function testHelloWorld(): void
    {
        $test =  $this->testInstance;
        $this->assertSame('Hello world', $test->helloWorld());
    }
}
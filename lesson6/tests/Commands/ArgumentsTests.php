<?php

namespace tests\Commands;

use PHPUnit\Framework\TestCase;
use src\Commands\Arguments;
use src\Exceptions\ArgumentsException;

class ArgumentsTests extends TestCase
{
    public function testCanCreateFromArgv(): void
    {
        $argv = ['key1=value1', 'key2=value2'];
        $arguments = Arguments::fromArgv($argv);

        $this->assertInstanceOf(Arguments::class, $arguments);
        $this->assertEquals('value1', $arguments->get('key1'));
        $this->assertEquals('value2', $arguments->get('key2'));
    }

    public function testIgnoresInvalidArgvFormat(): void
    {
        $argv = ['invalidformat', 'key=value'];
        $arguments = Arguments::fromArgv($argv);

        $this->assertInstanceOf(Arguments::class, $arguments);
        $this->assertFalse(array_key_exists('invalidformat', $argv));
        $this->assertEquals('value', $arguments->get('key'));
    }

    public function testThrowsExceptionForMissingArgument(): void
    {
        $this->expectException(ArgumentsException::class);

        $arguments = new Arguments(['key1' => 'value1']);
        $arguments->get('nonexistent_key');
    }

    public function testHandlesEmptyStringValues(): void
    {
        $argv = ['key1=', 'key2=value2'];
        $arguments = Arguments::fromArgv($argv);

        $this->assertEquals('', $arguments->get('key1'));
        $this->assertEquals('value2', $arguments->get('key2'));
    }
}
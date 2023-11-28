<?php

/**
 * Copyright Alexander Pas 2021.
 * Distributed under the Boost Software License, Version 1.0.
 * (See accompanying file LICENSE_1_0.txt or copy at https://www.boost.org/LICENSE_1_0.txt)
 */

declare(strict_types=1);

namespace Alexanderpas\Common\HTTP\Tests;

use Alexanderpas\Common\HTTP\StatusCode;

use Alexanderpas\Common\HTTP\StatusCodeClass;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

use ValueError;

/**
 * @covers \Alexanderpas\Common\HTTP\StatusCodeClass
 */
class StatusCodeClassNamedTest extends TestCase
{
    /**
     * @var StatusCodeClass[]
     */
    private array $cases = [];

    public function setUp(): void
    {
        $this->cases = StatusCodeClass::cases();
    }

    public function testFromNamesUppercase(): void
    {
        foreach ($this->cases as $case) {
            $name = strtoupper($case->name);
            $codeClass = StatusCodeClass::fromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    public function testFromNamesLowercase(): void
    {
        foreach ($this->cases as $case) {
            $name = strtolower($case->name);
            $codeClass = StatusCodeClass::fromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    public function testFromNamesTitlecase(): void
    {
        foreach ($this->cases as $case) {
            $name = ucfirst(strtolower($case->name));
            $codeClass = StatusCodeClass::fromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    public function testFromNamesInvertedTitlecase(): void
    {
        foreach ($this->cases as $case) {
            $name = lcfirst(strtoupper($case->name));
            $codeClass = StatusCodeClass::fromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    public function testTryFromNamesUppercase(): void
    {
        foreach ($this->cases as $case) {
            $name = strtoupper($case->name);
            $codeClass = StatusCodeClass::tryFromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    public function testTryFromNamesLowercase(): void
    {
        foreach ($this->cases as $case) {
            $name = strtolower($case->name);
            $codeClass = StatusCodeClass::tryFromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    public function testTryFromNamesTitlecase(): void
    {
        foreach ($this->cases as $case) {
            $name = ucfirst(strtolower($case->name));
            $codeClass = StatusCodeClass::tryFromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    public function testTryFromNamesInvertedTitlecase(): void
    {
        foreach ($this->cases as $case) {
            $name = lcfirst(strtoupper($case->name));
            $codeClass = StatusCodeClass::tryFromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    /**
     * @dataProvider namesProvider
     */
    public function testTryFromNamesNonAlphanumeric(): void
    {
        foreach ($this->cases as $case) {
            $name = lcfirst(strtoupper($case->name));
            $codeClass = StatusCodeClass::tryFromName(name: $name);
            Assert::assertThat($codeClass, Assert::identicalTo($case));
        }
    }

    public static function namesProvider(): array
    {
        return [
            'client_error' => [
                'Client_Error' => StatusCodeClass::ClientError,
            ],
            'client error' => [
                'client error' => StatusCodeClass::ClientError,
            ],
            'client%20error' => [
                'client%20error' => StatusCodeClass::ClientError,
            ],
            'server_error' => [
                'Server_Error' => StatusCodeClass::ServerError,
            ],
            'server error' => [
                'server error' => StatusCodeClass::ServerError,
            ],
            'server%20error' => [
                'server%20error' => StatusCodeClass::ServerError,
            ],
            'i n f o r m a t i o n a l' => [
                'i n f o r m a t i o n a l' => StatusCodeClass::Informational,
            ],
        ];
    }

    public function testNullFromName(): void
    {
        $codeClass = StatusCodeClass::tryFromName(name: null);
        Assert::assertThat($codeClass, Assert::isNull());
    }

    public function testInvalidFromName(): void
    {
        $invalidName = 'INVALID';
        $codeClass = StatusCodeClass::tryFromName(name: $invalidName);
        Assert::assertThat($codeClass, Assert::isNull());
        $this->expectException(ValueError::class);
        StatusCodeClass::fromName(name: $invalidName);
    }

    /**
     * @dataProvider IntegerProvider
     */
    public function testFromInteger(int $integer, StatusCodeClass $expectedClass): void
    {
        $codeClass = StatusCodeClass::fromInteger(integer: $integer);
        Assert::assertThat($codeClass, Assert::identicalTo($expectedClass));
    }

    /**
     * @dataProvider IntegerProvider
     */
    public function testTryFromInteger(int $integer, StatusCodeClass $expectedClass): void
    {
        $codeClass = StatusCodeClass::tryFromInteger(integer: $integer);
        Assert::assertThat($codeClass, Assert::identicalTo($expectedClass));
    }

    public static function IntegerProvider(): array
    {
        return [
            'Unknown code 99 - server error' => [99, StatusCodeClass::ServerError],
            'Unknown code 199 - informational' => [199, StatusCodeClass::Informational],
            'Known code 200 - successful' => [200, StatusCodeClass::Successful],
            'Unknown Code 255 - successful' => [255, StatusCodeClass::Successful],
            'Known Code 300 - redirection' => [300, StatusCodeClass::Redirection],
            'Unknown Code 355 - redirection' => [355, StatusCodeClass::Redirection],
            'Known Code 400 - client error' => [400, StatusCodeClass::ClientError],
            'Unknown Code 455 - client error' => [455, StatusCodeClass::ClientError],
            'Known Code 500 - server error' => [500, StatusCodeClass::ServerError],
            'Unknown Code 555 - server error' => [555, StatusCodeClass::ServerError],
            'Unknown Code 600 - server error' => [600, StatusCodeClass::ServerError],
            'Unknown Code 700 - server error' => [700, StatusCodeClass::ServerError],
            'Unknown Code 9999 - server error' => [9999, StatusCodeClass::ServerError],
        ];
    }

    public function testNullTryFromInteger(): void
    {
        $codeClass = StatusCodeClass::tryFromInteger(integer: null);
        Assert::assertThat($codeClass, Assert::isNull());
    }

    public function testFromStatusCode(): void
    {
        foreach (StatusCode::cases() as $code) {
            $codeClass = StatusCodeClass::fromStatusCode(statusCode: $code);

            $expectedClass = match (true) {
                $code->value >= 500 => StatusCodeClass::ServerError,
                $code->value >= 400 => StatusCodeClass::ClientError,
                $code->value >= 300 => StatusCodeClass::Redirection,
                $code->value >= 200 => StatusCodeClass::Successful,
                $code->value >= 100 => StatusCodeClass::Informational,
            };

            Assert::assertThat($codeClass, Assert::identicalTo($expectedClass));
        }
    }
}

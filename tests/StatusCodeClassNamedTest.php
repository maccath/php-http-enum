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
use ECSPrefix20211002\Symfony\Contracts\HttpClient\Test\HttpClientTestCase;
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

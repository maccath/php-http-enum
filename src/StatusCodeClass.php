<?php

/**
 * Copyright Alexander Pas 2021.
 * Distributed under the Boost Software License, Version 1.0.
 * (See accompanying file LICENSE_1_0.txt or copy at https://www.boost.org/LICENSE_1_0.txt)
 */

declare(strict_types=1);

namespace Alexanderpas\Common\HTTP;

use ValueError;

/**
 * Classes of response status codes as defined in RFC9110
 *
 * @see https://www.rfc-editor.org/rfc/rfc9110.html#section-15
 */
enum StatusCodeClass
{
    case Informational;
    case Successful;
    case Redirection;
    case ClientError;
    case ServerError;

    private const INFORMATIONAL = 'informational';
    private const SUCCESSFUL = 'successful';
    private const REDIRECTION = 'redirection';
    private const CLIENT_ERROR = 'clienterror';
    private const SERVER_ERROR = 'servererror';

    public static function fromName(string $name): StatusCodeClass
    {
        $codeClass = self::tryFromName($name);

        if (is_null($codeClass)) {
            $enumName = self::class;
            throw new ValueError("$name is not a valid name for enum \"$enumName\"");
        }

        return $codeClass;
    }

    public static function fromInteger(int $integer): StatusCodeClass
    {
        return match (true) {
            $integer >= 500 || $integer < 100 => StatusCodeClass::ServerError,
            $integer >= 400 => StatusCodeClass::ClientError,
            $integer >= 300 => StatusCodeClass::Redirection,
            $integer >= 200 => StatusCodeClass::Successful,
            $integer >= 100 => StatusCodeClass::Informational,
        };
    }

    public static function fromStatusCode(StatusCode $statusCode): StatusCodeClass
    {
        return $statusCode->getStatusCodeClass();
    }

    public static function tryFromName(?string $name): ?StatusCodeClass
    {
        if ($name === null) {
            return null;
        }

        $name = preg_replace('/\W/', '', strtolower($name));

        return match ($name) {
            self::INFORMATIONAL => self::Informational,
            self::SUCCESSFUL => self::Successful,
            self::REDIRECTION => self::Redirection,
            self::CLIENT_ERROR => self::ClientError,
            self::SERVER_ERROR => self::ServerError,
            default => null,
        };
    }

    public static function tryFromInteger(?int $integer): ?StatusCodeClass
    {
        if (is_null($integer)) {
            return null;
        }

        $statusCode = StatusCode::tryFromInteger($integer);

        if (!is_null($statusCode)) {
            return self::fromStatusCode($statusCode);
        }

        return self::fromInteger($integer);
    }
}

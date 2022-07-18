<?php
declare(strict_types=1);

namespace ErkEnes\MonorepoSplit;

use ErkEnes\MonorepoSplit\Exception\ConfigurationException;

final class PublicAccessTokenResolver
{
    private const ACCESS_TOKEN = 'ACCESS_TOKEN';
    private const INPUT_ACCESS_TOKEN = 'INPUT_ACCESS_TOKEN';

    /**
     * @var string[]
     */
    private const POSSIBLE_TOKEN_ENVS = [
        self::ACCESS_TOKEN,
        self::INPUT_ACCESS_TOKEN
    ];

    /**
     * @param array<string, mixed> $env
     */
    public function resolve(array $env): string
    {
        if (isset($env[self::ACCESS_TOKEN])) {
            return $env[self::ACCESS_TOKEN];
        }
        if (isset($env[self::INPUT_ACCESS_TOKEN])) {
            return $env[self::INPUT_ACCESS_TOKEN];
        }

        $message = sprintf(
            'Public access token is missing, add it via: "%s"',
            implode('", "', self::POSSIBLE_TOKEN_ENVS)
        );

        throw new ConfigurationException($message);
    }
}

<?php
declare(strict_types=1);

namespace ErkEnes\MonorepoSplit;

use ErkEnes\MonorepoSplit\Exception\ConfigurationException;

final class PublicAccessTokenResolver
{
    private const GITHUB_TOKEN = 'GITHUB_TOKEN';

    /**
     * @var string[]
     */
    private const POSSIBLE_TOKEN_ENVS = [
        self::GITHUB_TOKEN
    ];

    /**
     * @param array<string, mixed> $env
     */
    public function resolve(array $env): string
    {
        if (isset($env[self::GITHUB_TOKEN])) {
            return $env[self::GITHUB_TOKEN];
        }

        $message = sprintf(
            'Public access token is missing, add it via: "%s"',
            implode('", "', self::POSSIBLE_TOKEN_ENVS)
        );

        throw new ConfigurationException($message);
    }
}

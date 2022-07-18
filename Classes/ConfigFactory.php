<?php
declare(strict_types=1);

namespace ErkEnes\MonorepoSplit;

use ErkEnes\MonorepoSplit\Exception\ConfigurationException;

class ConfigFactory
{
    private const GITHUB = 'GITHUB';
    private const DEFAULT_BRANCH = 'main';

    private PublicAccessTokenResolver $publicAccessTokenResolver;

    public function __construct()
    {
        $this->publicAccessTokenResolver = new PublicAccessTokenResolver();
    }

    public function create(array $env): Config
    {
        $accessToken = $this->publicAccessTokenResolver->resolve($env);

        return $this->createFormEnv($env, $accessToken, self::GITHUB);
    }

    protected function createFormEnv(array $env, string $accessToken, string $ciPlatform): Config
    {
        $envPrefix = $ciPlatform === self::GITHUB ? 'INPUT_' : '';

        return new Config(
            repositoryProtocol: $env[$envPrefix . 'REPOSITORY_PROTOCOL'] ?? throw new ConfigurationException('Repository Protocol is missing'),
            repositoryHost: $env[$envPrefix . 'REPOSITORY_HOST'] ?? throw new ConfigurationException('Repository Host is missing'),
            repositoryOrganization: $env[$envPrefix . 'REPOSITORY_ORGANIZATION'] ?? throw new ConfigurationException('Repository Organization is missing'),
            repositoryName: $env[$envPrefix . 'REPOSITORY_NAME'] ?? throw new ConfigurationException('Repository Name is missing'),
            accessToken: $accessToken ?? throw new ConfigurationException('Access Token is missing'),
            defaultBranch: $env[$envPrefix . 'DEFAULT_BRANCH'] ?? self::DEFAULT_BRANCH,
            targetBranch: $env[$envPrefix . 'TARGET_BRANCH'] ?? throw new ConfigurationException('Target Branch is missing'),
            packageDirectory: $env[$envPrefix . 'PACKAGE_DIRECTORY'] ?? throw new ConfigurationException('Package Directory is missing'),
            remoteRepository: $env[$envPrefix . 'REMOTE_REPOSITORY'] ?? throw new ConfigurationException('Remote Repository is missing'),
            remoteRepositoryAccessToken: $env[$envPrefix . 'REMOTE_REPOSITORY_ACCESS_TOKEN'] ?? null,
        );
    }
}

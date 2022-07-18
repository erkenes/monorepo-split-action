<?php
declare(strict_types=1);

namespace ErkEnes\MonorepoSplit;

class Config
{
    public function __construct(
        private readonly string $repositoryProtocol,
        private readonly string $repositoryHost,
        private readonly string $repositoryOrganization,
        private readonly string $repositoryName,
        private readonly string $accessToken,
        private readonly string $defaultBranch,
        private readonly string $targetBranch,
        private readonly string $packageDirectory,
        private readonly string $remoteRepository,
        private readonly ?string $remoteRepositoryAccessToken = null
    )
    {
    }

    /**
     * @return string
     */
    public function getRepositoryProtocol(): string
    {
        return $this->repositoryProtocol;
    }

    /**
     * @return string
     */
    public function getRepositoryHost(): string
    {
        return $this->repositoryHost;
    }

    /**
     * @return string
     */
    public function getRepositoryOrganization(): string
    {
        return $this->repositoryOrganization;
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param bool $withAccessToken
     * @return string
     */
    public function getRepositoryUrl(bool $withAccessToken = false): string
    {
        $repositoryUrl = $this->repositoryHost . '/' . $this->repositoryOrganization . '/' . $this->repositoryName . '.git';

        return $withAccessToken ? $this->repositoryProtocol . $this->accessToken . '@' . $repositoryUrl : $this->repositoryProtocol . $repositoryUrl;
    }

    public function getSplits(): array
    {
        return [
            $this->getPackageDirectory() . ':' . $this->getRemoteRepository()
        ];
    }

    /**
     * @return string
     */
    public function getDefaultBranch(): string
    {
        return $this->defaultBranch;
    }

    /**
     * @return string
     */
    public function getTargetBranch(): string
    {
        return $this->targetBranch;
    }

    /**
     * @return string
     */
    public function getPackageDirectory(): string
    {
        return $this->packageDirectory;
    }

    /**
     * @return string
     */
    public function getRemoteRepository(): string
    {
        return $this->remoteRepositoryAccessToken
            ? $this->insertStringBetween($this->remoteRepository, 'https://', $this->remoteRepositoryAccessToken . '@')
            : $this->remoteRepository;
    }

    /**
     * @return string|null
     */
    public function getRemoteRepositoryAccessToken(): ?string
    {
        return $this->remoteRepositoryAccessToken;
    }

    private function insertStringBetween ($string, $keyword, $body): string
    {
        return substr_replace($string, $body, strpos($string, $keyword) + strlen($keyword), 0);
    }
}

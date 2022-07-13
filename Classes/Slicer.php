<?php
declare(strict_types=1);

namespace ErkEnes\MonorepoSplit;

use ErkEnes\MonorepoSplit\Exception\ConfigurationException;

class Slicer
{
    private const GIT_SUB_SPLIT_BINARY = '/data/bin/git-subsplit.sh';
    private const WORKING_DIRECTORY = '/data/git-subsplit-temporary';

    protected Config $config;

    /**
     * Read the environment variables and save them to the config
     */
    public function __construct()
    {
        $configFactory = new ConfigFactory();
        try {
            $this->config = $configFactory->create(getenv());
        } catch (ConfigurationException $configurationException) {
            $this->error($configurationException->getMessage());
            exit(0);
        }
    }

    /**
     * Runner to slice a mono-repository
     * @return void
     */
    public function run(): void
    {
        $targetRef = $this->config->getTargetBranch(true);
        $repositoryUrl = $this->config->getRepositoryUrl(true);

        // This is required for the git-subsplit.sh
        putenv('DEFAULT_BRANCH=' . $this->config->getDefaultBranch());

        if ($this->config->getAllowedRefsPattern() !== null && preg_match($this->config->getAllowedRefsPattern(), $targetRef) !== 1) {
            echo sprintf('Skipping request (blacklisted reference detected: %s)', $targetRef) . PHP_EOL;
            exit(0);
        }

        $publishCommand = [
            sprintf(
                '%s publish --update %s',
                self::GIT_SUB_SPLIT_BINARY,
                escapeshellarg(implode(' ', $this->config->getSplits()))
            )
        ];

        if (preg_match('/refs\/tags\/(.+)$/', $targetRef, $matches)) {
            $publishCommand[] = escapeshellarg('--no-heads');
            $publishCommand[] = escapeshellarg(sprintf('--tags=%s', $matches[1]));
        } elseif (preg_match('/refs\/heads\/(.+)$/', $targetRef, $matches)) {
            $publishCommand[] = escapeshellarg('--no-tags');
            $publishCommand[] = escapeshellarg(sprintf('--heads=%s', $matches[1]));
        } else {
            echo sprintf('Skipping request (unexpected reference detected: %s)', $targetRef) . PHP_EOL;
            exit(0);
        }

        $projectWorkingDirectory = self::WORKING_DIRECTORY;
        if (!file_exists($projectWorkingDirectory)) {
            echo sprintf('Creating working directory (%s)', $projectWorkingDirectory) . PHP_EOL;
            mkdir($projectWorkingDirectory, 0750, true);
        }

        $subtreeCachePath = $projectWorkingDirectory . '/.subsplit/.git/subtree-cache';
        if (file_exists($subtreeCachePath)) {
            echo sprintf('Removing subtree-cache (%s)', $subtreeCachePath);
            passthru(sprintf('rm -rf %s', escapeshellarg($subtreeCachePath)));
        }

        $command = implode(' && ', [
            sprintf('cd %s', escapeshellarg($projectWorkingDirectory)),
            sprintf('( %s init %s || true )', self::GIT_SUB_SPLIT_BINARY, escapeshellarg($repositoryUrl)),
            implode(' ', $publishCommand)
        ]);
        passthru($command, $exitCode);
        if (0 !== $exitCode) {
            echo sprintf('Command %s had a problem, exit code %s', $command, $exitCode) . PHP_EOL;
            exit($exitCode);
        }
    }

    /**
     * Display an error message
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message): void
    {
        echo PHP_EOL . PHP_EOL . "\033[0;31m[ERROR] " . $message . "\033[0m" . PHP_EOL . PHP_EOL;
    }
}

<?php
declare(strict_types=1);

use ErkEnes\MonorepoSplit\Slicer;

// GitHub-Tokens for access
//putenv('INPUT_REMOTE_REPOSITORY_ACCESS_TOKEN=');
putenv('GITHUB_TOKEN=');

// The branch/head which should be sliced
//putenv('INPUT_TARGET_BRANCH=refs/tags/1.6.0');
putenv('INPUT_TARGET_BRANCH=refs/heads/main');

putenv('INPUT_REPOSITORY_PROTOCOL=https://');
putenv('INPUT_REPOSITORY_HOST=github.com');
putenv('INPUT_REPOSITORY_ORGANIZATION=erkenes');
putenv('INPUT_REPOSITORY_NAME=monorepo-split');
putenv('INPUT_DEFAULT_BRANCH=main');
putenv('INPUT_PACKAGE_DIRECTORY=LocalPackages/Package.One');
putenv('INPUT_REMOTE_REPOSITORY=https://github.com/erkenes/package-one.git');
putenv('INPUT_ALLOWED_REFS_PATTERN=/^refs\\/(tags|heads)\\/([0-9]+\\.[0-9]+(\\.[0-9]+)?|main)$/');

require_once '/data/Classes/autoload.php';

$slicer = new Slicer();
$slicer->run();

<?php
declare(strict_types=1);

use ErkEnes\MonorepoSplit\Slicer;

require_once '/data/Classes/autoload.php';

$slicer = new Slicer();
$slicer->run();

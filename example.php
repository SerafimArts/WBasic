<?php
/**
 * This file is part of WBasic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

use Railt\Io\File;
use Serafim\WBasic\Compiler;

require __DIR__ . '/vendor/autoload.php';

echo (new Compiler())
    ->generate(File::fromPathname(__DIR__ . '/example.bas'));

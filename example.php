<?php
/**
 * This file is part of WBasic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

use Serafim\WBasic\Compiler;

require __DIR__ . '/vendor/autoload.php';

$src = Railt\Io\File::fromSources('
   REM Example
   A = 23
   B = 42
   C = "asdasd" & "asdasdasd"
');

echo (new Compiler())->generate($src);

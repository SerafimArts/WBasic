<?php
/**
 * This file is part of WBasic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\WBasic;

use Railt\Io\Readable;

/**
 * Class Compiler
 */
class Compiler
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * Compiler constructor.
     */
    public function __construct()
    {
        $this->parser = new Parser();
        $this->builder = new Builder();
    }

    /**
     * @param Readable $sources
     * @return string
     * @throws \Railt\Compiler\Exception\UnrecognizedTokenException
     */
    public function generate(Readable $sources): string
    {
        $result = '<?php';

        foreach ($this->builder->each($this->parser->parse($sources)) as $operation) {
            $result .= "\n$operation";
        }

        return $result;
    }
}

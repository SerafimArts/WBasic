<?php
/**
 * This file is part of WBasic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\WBasic;

use Railt\Compiler\Exception\UnrecognizedTokenException;
use Railt\Parser\Ast\NodeInterface;
use Railt\Parser\Ast\RuleInterface;
use Serafim\WBasic\Builder\BuilderInterface;
use Serafim\WBasic\Builder\OperatorBuilder;
use Serafim\WBasic\Builder\ValueBuilder;
use Serafim\WBasic\Builder\VariableBuilder;

/**
 * Class Builder
 */
class Builder
{
    /**
     * @var string[]
     */
    private const RULES_MAPPING = [
        'VariableDefinition' => VariableBuilder::class,
        'Operator'           => OperatorBuilder::class,
        'Value'              => ValueBuilder::class,
    ];

    /**
     * @param RuleInterface $ast
     * @return \Generator
     * @throws UnrecognizedTokenException
     */
    public function each(RuleInterface $ast): \Generator
    {
        foreach ($ast->getChildren() as $child) {
            yield $this->build($child);
        }
    }

    /**
     * @param NodeInterface $ast
     * @return string
     * @throws UnrecognizedTokenException
     */
    private function build(NodeInterface $ast): string
    {
        $process = $this->builder($ast)->build($ast);

        if ($process instanceof \Generator) {
            while ($process->valid()) {
                $value = $process->current();

                if ($value instanceof RuleInterface) {
                    $value = $this->build($value);
                }

                $process->send($value);
            }

            return (string)$process->getReturn();
        }

        return (string)$process;
    }

    /**
     * @param NodeInterface $ast
     * @return BuilderInterface
     * @throws UnrecognizedTokenException
     */
    private function builder(NodeInterface $ast): BuilderInterface
    {
        $builder = self::RULES_MAPPING[$ast->getName()] ?? null;

        if ($builder === null) {
            throw new UnrecognizedTokenException('Unrecognized rule ' . $ast->getName());
        }

        return new $builder;
    }
}

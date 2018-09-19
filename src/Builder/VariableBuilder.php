<?php
/**
 * This file is part of WBasic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\WBasic\Builder;

use Railt\Parser\Ast\NodeInterface;

/**
 * Class VariableBuilder
 */
class VariableBuilder implements BuilderInterface
{
    /**
     * @param NodeInterface $ast
     * @return \Generator
     * @throws \Railt\Parser\Exception\InternalException
     * @throws \Railt\Parser\Exception\ParserException
     */
    public function build(NodeInterface $ast): \Generator
    {
        return \sprintf('$%s %s %s;',
            $ast->find('> :T_VAR')->value(),
            yield $ast->first('> Operator'),
            yield $ast->first('> Value')
        );
    }
}

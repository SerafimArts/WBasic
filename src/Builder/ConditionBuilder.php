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

class ConditionBuilder implements BuilderInterface
{
    public function build(NodeInterface $ast)
    {
        $expression = yield $ast->first('> #ExpressionDefinition');
        [$body, $else] = [[], []];

        foreach ($ast->first('> #ConditionBody') as $child) {
            $body[] = yield $child;
        }

        if ($elseExpr = $ast->first('> #ConditionElseDefinition')) {
            foreach ($elseExpr->first('> #ConditionBody') as $child) {
                $else[] = yield $child;
            }
        }

        $result = 'if (' . $expression .') {' . "\n    " .
            \implode("\n    ", $body) . "\n" .
        '}';

        if (\count($else)) {
            $result .= ' else {' . "\n    " .
                \implode("\n    ", $else) . "\n" .
            '}';
        }

        return $result;
    }
}

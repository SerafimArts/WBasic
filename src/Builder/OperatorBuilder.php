<?php
/**
 * This file is part of WBasic package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\WBasic\Builder;

use Railt\Parser\Ast\LeafInterface;
use Railt\Parser\Ast\NodeInterface;
use Railt\Parser\Ast\RuleInterface;
use Serafim\WBasic\Parser;

/**
 * Class OperatorBuilder
 */
class OperatorBuilder implements BuilderInterface
{
    /**
     * @param NodeInterface|RuleInterface $ast
     * @return string
     */
    public function build(NodeInterface $ast): string
    {
        $result = [];

        foreach ($ast->getChildren() as $token) {
            $result[] = $this->format($token);
        }

        return \implode(' ', $result);
    }

    /**
     * @param LeafInterface $operator
     * @return string
     */
    private function format(LeafInterface $operator): string
    {
        switch ($operator->getName()) {
            case Parser::T_LTE:
                return '<=';
            case Parser::T_LT:
                return '<';
            case Parser::T_GTE:
                return '>=';
            case Parser::T_GT:
                return '>';
            case Parser::T_NEQ:
                return '!==';
            case Parser::T_EQ:
                return '===';
            case Parser::T_NOT:
                return '!';
        }
    }
}

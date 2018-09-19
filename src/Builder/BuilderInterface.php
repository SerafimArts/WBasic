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
use Railt\Parser\Ast\RuleInterface;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface
{
    /**
     * @param NodeInterface $ast
     * @return \Generator|mixed
     */
    public function build(NodeInterface $ast);
}

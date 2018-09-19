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
 * Class ValueBuilder
 */
class ValueBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    private const CHAR_SEQUENCE_PATTERN = '/(?<!\\\\)\\\\(b|f|n|r|t)/u';

    /**
     * @param NodeInterface|RuleInterface $ast
     * @return \Generator|mixed|void
     */
    public function build(NodeInterface $ast): string
    {
        $result = [];

        foreach ($ast->getChildren() as $child) {
            switch ($child->getName()) {
                case Parser::T_NUMBER:
                    $result[] = $this->parseNumber($child);
                    break;
                case Parser::T_STRING:
                    $result[] = $this->parseString($child);
                    break;
            }
        }

        return \implode(' . ', $result);
    }

    /**
     * @param LeafInterface $leaf
     * @return string
     */
    private function parseString(LeafInterface $leaf): string
    {
        $string = $leaf->getValue(1);

        $string = $this->encodeSlashes($string);
        $string = $this->renderSpecialCharacters($string);
        $string = $this->decodeSlashes($string);

        return \sprintf('"%s"', \addcslashes($string, '"'));
    }

    /**
     * @param string $value
     * @return string
     */
    private function encodeSlashes(string $value): string
    {
        return \str_replace(['\\\\', '\\"'], ["\0", '"'], $value);
    }

    /**
     * Method for parsing special control characters.
     *
     * @see http://facebook.github.io/graphql/October2016/#sec-String-Value
     * @param string $body
     * @return string
     */
    private function renderSpecialCharacters(string $body): string
    {
        $callee = function (array $matches): string {
            [$char, $code] = [$matches[0], $matches[1]];
            switch ($code) {
                case 'b':
                    return "\u{0008}";
                case 'f':
                    return "\u{000C}";
                case 'n':
                    return "\u{000A}";
                case 'r':
                    return "\u{000D}";
                case 't':
                    return "\u{0009}";
            }
            return $char;
        };
        return @\preg_replace_callback(self::CHAR_SEQUENCE_PATTERN, $callee, $body) ?? $body;
    }

    /**
     * @param string $value
     * @return string
     */
    private function decodeSlashes(string $value): string
    {
        return \str_replace("\0", '\\', $value);
    }

    /**
     * @param LeafInterface $value
     * @return float|int
     */
    private function parseNumber(LeafInterface $value)
    {
        switch (true) {
            case $this->isHex($value):
                return $this->parseHex($value->getValue(1));
            case $this->isBinary($value):
                return $this->parseBin($value->getValue(1));
            case $this->isExponential($value):
                return $this->parseExponential($value->getValue());
            case $this->isFloat($value):
                return $this->parseFloat($value->getValue());
            case $this->isInt($value):
                return $this->parseInt($value->getValue());
        }

        return (float)$value->getValue();
    }

    /**
     * @param LeafInterface $leaf
     * @return bool
     */
    private function isHex(LeafInterface $leaf): bool
    {
        return $leaf->getName() === Parser::T_HEX_NUMBER;
    }

    /**
     * @param string $value
     * @return int
     */
    private function parseHex(string $value): int
    {
        return \hexdec($value);
    }

    /**
     * @param LeafInterface $leaf
     * @return bool
     */
    private function isBinary(LeafInterface $leaf): bool
    {
        return $leaf->getName() === Parser::T_BIN_NUMBER;
    }

    /**
     * @param string $value
     * @return int
     */
    private function parseBin(string $value): int
    {
        return \bindec($value);
    }

    /**
     * @param LeafInterface $leaf
     * @return bool
     */
    private function isExponential(LeafInterface $leaf): bool
    {
        return \substr_count(\strtolower($leaf->getValue()), 'e') !== 0;
    }

    /**
     * @param string $value
     * @return float
     */
    private function parseExponential(string $value): float
    {
        return (float)$value;
    }

    /**
     * @param LeafInterface $leaf
     * @return bool
     */
    private function isFloat(LeafInterface $leaf): bool
    {
        return \substr_count($leaf->getValue(), '.') !== 0;
    }

    /**
     * @param string $value
     * @return float
     */
    private function parseFloat(string $value): float
    {
        return (float)$value;
    }

    /**
     * @param LeafInterface $leaf
     * @return bool
     */
    private function isInt(LeafInterface $leaf): bool
    {
        return $leaf->getName() === Parser::T_NUMBER && \substr_count($leaf->getValue(), '.') === 0;
    }

    /**
     * @param string $value
     * @return int
     */
    private function parseInt(string $value): int
    {
        return $value >> 0;
    }
}

<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\WBasic;

use Railt\Lexer\Factory;
use Railt\Lexer\LexerInterface;
use Railt\Parser\Driver\Llk;
use Railt\Parser\Driver\Stateful;
use Railt\Parser\Grammar;
use Railt\Parser\ParserInterface;
use Railt\Parser\Rule\Alternation;
use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Repetition;
use Railt\Parser\Rule\Terminal;
use Railt\Parser\GrammarInterface;

/**
 * --- DO NOT EDIT THIS FILE ---
 *
 * Class Parser has been auto-generated.
 * Generated at: 19-09-2018 08:06:15
 *
 * --- DO NOT EDIT THIS FILE ---
 */
class Parser extends Stateful
{
    public const T_COMMENT = 'T_COMMENT';
    public const T_OP_PRINT = 'T_OP_PRINT';
    public const T_OP_AND = 'T_OP_AND';
    public const T_OP_OR = 'T_OP_OR';
    public const T_OP_XOR = 'T_OP_XOR';
    public const T_VAR = 'T_VAR';
    public const T_NOT = 'T_NOT';
    public const T_LTE = 'T_LTE';
    public const T_GTE = 'T_GTE';
    public const T_NEQ = 'T_NEQ';
    public const T_LT = 'T_LT';
    public const T_GT = 'T_GT';
    public const T_EQ = 'T_EQ';
    public const T_AND = 'T_AND';
    public const T_OR = 'T_OR';
    public const T_HEX_NUMBER = 'T_HEX_NUMBER';
    public const T_BIN_NUMBER = 'T_BIN_NUMBER';
    public const T_STRING = 'T_STRING';
    public const T_NUMBER = 'T_NUMBER';
    public const T_WHITESPACE = 'T_WHITESPACE';
    public const T_SEMICOLON = 'T_SEMICOLON';

    /**
     * Lexical tokens list.
     *
     * @var string[]
     */
    protected const LEXER_TOKENS = [
        self::T_COMMENT => 'REM[^\\n]+',
        self::T_OP_PRINT => 'PRINT\\b',
        self::T_OP_AND => 'AND\\b',
        self::T_OP_OR => 'OR\\b',
        self::T_OP_XOR => 'XOR\\b',
        self::T_VAR => '[a-zA-Z]\\b',
        self::T_NOT => 'NOT\\b',
        self::T_LTE => '<=',
        self::T_GTE => '>=',
        self::T_NEQ => '<>',
        self::T_LT => '<',
        self::T_GT => '>',
        self::T_EQ => '=',
        self::T_AND => '&',
        self::T_OR => '\\|',
        self::T_HEX_NUMBER => '\\-?0x([0-9a-fA-F]+)',
        self::T_BIN_NUMBER => '\\-?0b([0-1]+)',
        self::T_STRING => '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
        self::T_NUMBER => '\\-?(?:0|[1-9][0-9]*)(?:\\.[0-9]+)?(?:[eE][\\+\\-]?[0-9]+)?',
        self::T_WHITESPACE => '\\s+',
        self::T_SEMICOLON => ';',
    ];

    /**
     * List of skipped tokens.
     *
     * @var string[]
     */
    protected const LEXER_SKIPPED_TOKENS = [
        'T_COMMENT',
        'T_WHITESPACE',
        'T_SEMICOLON',
    ];

    /**
     * @var int
     */
    protected const LEXER_FLAGS = Factory::LOOKAHEAD;

    /**
     * List of rule delegates.
     *
     * @var string[]
     */
    protected const PARSER_DELEGATES = [
    ];

    /**
     * Parser root rule name.
     *
     * @var string
     */
    protected const PARSER_ROOT_RULE = 'Program';

    /**
     * @return ParserInterface
     * @throws \InvalidArgumentException
     * @throws \Railt\Lexer\Exception\BadLexemeException
     */
    protected function boot(): ParserInterface
    {
        return new Llk($this->bootLexer(), $this->bootGrammar());
    }

    /**
     * @return LexerInterface
     * @throws \InvalidArgumentException
     * @throws \Railt\Lexer\Exception\BadLexemeException
     */
    protected function bootLexer(): LexerInterface
    {
        return Factory::create(static::LEXER_TOKENS, static::LEXER_SKIPPED_TOKENS, static::LEXER_FLAGS);
    }

    /**
     * @return GrammarInterface
     */
    protected function bootGrammar(): GrammarInterface
    {
        return new Grammar([
            new Repetition(0, 0, -1, '__program', null), 
            (new Concatenation('Program', [0], 'Program'))->setDefaultId('Program'), 
            new Concatenation('__program', ['VariableDefinition'], null), 
            new Terminal(3, 'T_NOT', true), 
            new Concatenation(4, [3, '__operator'], 'Operator'), 
            new Concatenation(5, ['__operator'], null), 
            new Concatenation(6, [5], 'Operator'), 
            (new Alternation('Operator', [4, 6], null))->setDefaultId('Operator'), 
            new Terminal(8, 'T_LTE', true), 
            new Terminal(9, 'T_GTE', true), 
            new Terminal(10, 'T_NEQ', true), 
            new Terminal(11, 'T_LT', true), 
            new Terminal(12, 'T_GT', true), 
            new Terminal(13, 'T_EQ', true), 
            new Alternation('__operator', [8, 9, 10, 11, 12, 13], null), 
            new Concatenation(15, ['String'], 'Value'), 
            new Concatenation(16, ['Number'], 'Value'), 
            new Concatenation(17, ['Variable'], null), 
            new Concatenation(18, [17], 'Value'), 
            (new Alternation('Value', [15, 16, 18], null))->setDefaultId('Value'), 
            new Terminal(20, 'T_STRING', true), 
            new Terminal(21, 'T_STRING', true), 
            new Terminal(22, 'T_AND', false), 
            new Concatenation(23, ['String'], null), 
            new Concatenation(24, [21, 22, 23], null), 
            new Alternation('String', [20, 24], null), 
            new Terminal(26, 'T_NUMBER', true), 
            new Terminal(27, 'T_HEX_NUMBER', true), 
            new Terminal(28, 'T_BIN_NUMBER', true), 
            new Alternation('Number', [26, 27, 28], null), 
            new Terminal('Variable', 'T_VAR', true), 
            new Terminal(31, 'T_VAR', true), 
            new Concatenation(32, ['Value'], null), 
            (new Concatenation('VariableDefinition', [31, '__variableDefinitionOperator', 32], 'VariableDefinition'))->setDefaultId('VariableDefinition'), 
            new Terminal(34, 'T_EQ', true), 
            new Concatenation('__variableDefinitionOperator', [34], 'Operator')
        ], static::PARSER_ROOT_RULE, static::PARSER_DELEGATES);
    }
}
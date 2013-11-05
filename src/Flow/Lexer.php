<?php

namespace Flow;

class Lexer
{
    protected $source;
    protected $line;
    protected $char;
    protected $cursor;
    protected $position;
    protected $queue;
    protected $end;
    protected $trim;

    const BLOCK_START_TAG      = '{%';
    const BLOCK_START_TAG_TRIM = '{%-';
    const BLOCK_END_TAG        = '%}';
    const BLOCK_END_TAG_TRIM   = '-%}';

    const COMMENT_START_TAG      = '{#';
    const COMMENT_START_TAG_TRIM = '{#-';
    const COMMENT_END_TAG        = '#}';
    const COMMENT_END_TAG_TRIM   = '-#}';

    const OUTPUT_START_TAG      = '{{';
    const OUTPUT_START_TAG_TRIM = '{{-';
    const OUTPUT_END_TAG        = '}}';
    const OUTPUT_END_TAG_TRIM   = '-}}';

    const POSITION_TEXT  = 0;
    const POSITION_BLOCK = 1;
    const POSITION_OUTPUT   = 2;

    const REGEX_CONSTANT = '/true\b | false\b | null\b/Ax';
    const REGEX_NAME     = '/[a-zA-Z_][a-zA-Z0-9_]*/A';
    const REGEX_NUMBER   = '/[0-9][0-9_]*(?:\.[0-9][0-9_]*)?/A';
    const REGEX_STRING   = '/(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|
        \'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')/Axsmu';
    const REGEX_OPERATOR = '/and\b|xor\b|or\b|not\b|in\b|
        =>|<>|<=?|>=?|[!=]==|[!=]?=|\.\.|[\[\]().,%*\/+|?:\-@~]/Ax';

    public function __construct($source)
    {
        $this->source   = preg_replace("/(\r\n|\r|\n)/", "\n", $source);
        $this->line     = 1;
        $this->char     = 1;
        $this->cursor   = 0;
        $this->position = self::POSITION_TEXT;
        $this->queue    = array();
        $this->end      = strlen($this->source);
        $this->trim     = false;
    }

    public function tokenize($stream = true)
    {
        do {
            $tokens[] = $token = $this->next();
        } while ($token->getType() !== Token::EOF_TYPE);

        return $stream ? new TokenStream($tokens) : $tokens;
    }

    protected function next()
    {
        if (!empty($this->queue)) {
            return array_shift($this->queue);
        }

        if ($this->cursor >= $this->end) {
            return Token::tokenEOF('', $this->line, $this->char);
        }

        switch ($this->position) {

        case self::POSITION_TEXT:
            $this->queue = $this->lexText();
            break;

        case self::POSITION_BLOCK:
            $this->queue = $this->lexBlock();
            break;

        case self::POSITION_OUTPUT:
            $this->queue = $this->lexOutput();
            break;
        }

        return $this->next();
    }

    public function adjustLineChar($string)
    {
        if (($nl = substr_count($string, "\n")) > 0) {
            $this->line += $nl;
            $this->char = strlen($string) - strrpos($string, "\n");
        } else {
            $this->char += strlen($string);
        }
    }

    protected function lexText()
    {
        $match = null;
        $tokens = array();

        // all text
        if (!preg_match('/(.*?)(' .
            preg_quote(self::COMMENT_START_TAG_TRIM) .'|' .
            preg_quote(self::COMMENT_START_TAG) . '|' .
            preg_quote(self::OUTPUT_START_TAG_TRIM) . '|' .
            preg_quote(self::OUTPUT_START_TAG) . '|' .
            preg_quote(self::BLOCK_START_TAG_TRIM) . '|' .
            preg_quote(self::BLOCK_START_TAG) . ')/As', $this->source, $match,
                null, $this->cursor)
        ) {
            $text = substr($this->source, $this->cursor);
            if ($this->trim) {
                $text = preg_replace("/^[ \t]*\n?/", '', $text);
                $this->trim = false;
            }
            $tokens[] = Token::tokenText($text, $this->line, $this->char);
            $this->adjustLineChar($text);
            $this->cursor = $this->end;
            return $tokens;
        }

        $this->cursor += strlen($match[0]);

        // text first
        $text  = $match[1];
        $token = $match[2];

        if (strlen($text)) {
            if ($this->trim) {
                $text = preg_replace("/^[ \t]*\n?/", '', $text);
                $this->trim = false;
            }
            if ($token == self::COMMENT_START_TAG_TRIM ||
                $token == self::BLOCK_START_TAG_TRIM ||
                $token == self::OUTPUT_START_TAG_TRIM) {
                $text = rtrim($text, " \t");
            }
            $tokens[] = Token::tokenText($text, $this->line, $this->char);
        }

        $this->adjustLineChar($match[1]);

        switch ($token) {

        case self::COMMENT_START_TAG_TRIM:
        case self::COMMENT_START_TAG:
            if (preg_match('/.*?(' .
                preg_quote(self::COMMENT_END_TAG_TRIM) . '|' .
                preg_quote(self::COMMENT_END_TAG) . ')/As',
                    $this->source, $match, null, $this->cursor)
            ) {
                if ($match[1] == self::COMMENT_END_TAG_TRIM) {
                    $this->trim = true;
                }
                $this->cursor += strlen($match[0]);
                $this->adjustLineChar($match[0]);
            }
            break;

        case self::BLOCK_START_TAG_TRIM:
        case self::BLOCK_START_TAG:
            if (preg_match('/(\s*raw\s*)(' .
                preg_quote(self::BLOCK_END_TAG_TRIM) . '|' .
                preg_quote(self::BLOCK_END_TAG) . ')(.*?)(' .
                preg_quote(self::BLOCK_START_TAG_TRIM) . '|' .
                preg_quote(self::BLOCK_START_TAG) . ')(\s*endraw\s*)(' .
                preg_quote(self::BLOCK_END_TAG_TRIM) . '|' .
                preg_quote(self::BLOCK_END_TAG) . ')/As',
                    $this->source, $match, null, $this->cursor)
            ) {
                $raw = $match[3];
                if ($match[2] == self::BLOCK_END_TAG_TRIM) {
                    $raw = preg_replace("/^[ \t]*\n?/", '', $raw);
                }
                if ($match[4] == self::BLOCK_START_TAG_TRIM) {
                    $raw = rtrim($raw, " \t");
                }
                if ($match[6] == self::BLOCK_END_TAG_TRIM) {
                    $this->trim = true;
                }
                $before = $token . $match[1] . $match[2];
                $after  = $match[3] . $match[4] . $match[5] . $match[6];
                $this->cursor += strlen($match[0]);
                $this->adjustLineChar($before);
                $tokens[] = Token::tokenText($raw, $this->line, $this->char);
                $this->adjustLineChar($after);
                $this->position = self::POSITION_TEXT;
            } else {
                $tokens[] = Token::tokenBlockStart($token, $this->line, $this->char);
                $this->adjustLineChar($token);
                $this->position = self::POSITION_BLOCK;
            }
            break;

        case self::OUTPUT_START_TAG_TRIM:
        case self::OUTPUT_START_TAG:
            $tokens[] = Token::tokenOutputStart($token, $this->line, $this->char);
            $this->adjustLineChar($token);
            $this->position = self::POSITION_OUTPUT;
            break;

        }

        return $tokens;
    }

    protected function lexBlock()
    {
        $tokens = array();
        $match = null;

        if (preg_match('/(\s*)(' .
            preg_quote(self::BLOCK_END_TAG_TRIM) . '|' .
            preg_quote(self::BLOCK_END_TAG) . ')/A',
                $this->source, $match, null, $this->cursor)
        ) {
            if ($match[2] == self::BLOCK_END_TAG_TRIM) {
                $this->trim = true;
            }
            $this->cursor += strlen($match[0]);
            $this->adjustLineChar($match[1]);
            $tokens[] = Token::tokenBlockEnd($match[2], $this->line, $this->char);
            $this->adjustLineChar($match[2]);
            $this->position = self::POSITION_TEXT;

            return $tokens;
        }
        return $this->lexExpression();
    }

    protected function lexOutput()
    {
        $tokens = array();
        $match = null;

        if (preg_match('/(\s*)(' .
            preg_quote(self::OUTPUT_END_TAG_TRIM) . '|' .
            preg_quote(self::OUTPUT_END_TAG) . ')/A',
                $this->source, $match, null, $this->cursor)
        ) {
            if ($match[2] == self::OUTPUT_END_TAG_TRIM) {
                $this->trim = true;
            }
            $this->cursor += strlen($match[0]);
            $this->adjustLineChar($match[1]);
            $tokens[] = Token::tokenOutputEnd($match[2], $this->line, $this->char);
            $this->adjustLineChar($match[2]);
            $this->position = self::POSITION_TEXT;

            return $tokens;
        }
        return $this->lexExpression();
    }

    protected function lexExpression()
    {
        $tokens = array();
        $match = null;

        // eat whitespace
        if (preg_match('/\s+/A', $this->source, $match, null, $this->cursor)) {
            $this->cursor += strlen($match[0]);
            $this->adjustLineChar($match[0]);
        }

        if (preg_match(self::REGEX_NUMBER, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            $number = str_replace('_', '', $match[0]);
            $tokens[] = Token::tokenNumber($number, $this->line, $this->char);
            $this->adjustLineChar($match[0]);

        } elseif (preg_match(self::REGEX_OPERATOR, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            $operator = $match[0];
            $tokens[] = Token::tokenOperator($operator, $this->line, $this->char);
            $this->adjustLineChar($match[0]);

        } elseif (preg_match(self::REGEX_CONSTANT, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            $constant = $match[0];
            $tokens[] = Token::tokenConstant($constant, $this->line, $this->char);
            $this->adjustLineChar($match[0]);

        } elseif (preg_match(self::REGEX_NAME, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            $name = $match[0];
            $tokens[] = Token::tokenName($name, $this->line, $this->char);
            $this->adjustLineChar($match[0]);

        } elseif (preg_match(self::REGEX_STRING, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            $string = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
            $tokens[] = Token::tokenString($string, $this->line, $this->char);
            $this->adjustLineChar($match[0]);

        } elseif ($this->position == self::POSITION_BLOCK &&
            preg_match('/(.+?)\s*(' .
            preg_quote(self::BLOCK_END_TAG_TRIM) . '|' .
            preg_quote(self::BLOCK_END_TAG) . ')/As',
                $this->source, $match, null, $this->cursor)
        ) {
            // a catch-all text token
            $this->cursor += strlen($match[1]);
            $text = $match[1];
            $tokens[] = Token::tokenText($text, $this->line, $this->char);
            $this->adjustLineChar($match[1]);

        } elseif ($this->position == self::POSITION_OUTPUT &&
            preg_match('/(.+?)\s*(' . preg_quote(self::OUTPUT_END_TAG) . ')/As',
                $this->source, $match, null, $this->cursor)
        ) {
            $this->cursor += strlen($match[1]);
            $text = $match[1];
            $tokens[] = Token::tokenText($text, $this->line, $this->char);
            $this->adjustLineChar($match[1]);

        } else {
            $text = substr($this->source, $this->cursor);
            $this->cursor += $this->end;
            $tokens[] = Token::tokenText($text, $this->line, $this->char);
            $this->adjustLineChar($text);
        }

        return $tokens;
    }
}

class SyntaxError extends \Exception
{
    protected $token;

    public function __construct($message, $token)
    {
        $this->token = $token;
        parent::__construct($message . ' in line ' . $token->getLine() . ' char ' . $token->getChar());
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}

class TokenStream
{
    protected $tokens;
    protected $currentToken;
    protected $queue;
    protected $cursor;
    protected $eos;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->currentToken = null;
        $this->queue = array();
        $this->cursor = 0;
        $this->eos = false;
        $this->next();
    }

    public function next($queue = true)
    {
        if ($this->eos) {
            return $this->currentToken;
        }

        $token = $this->tokens[$this->cursor++];

        $old = $this->currentToken;

        $this->currentToken = $token;

        $this->eos = ($token->getType() === Token::EOF_TYPE);

        return $old;
    }

    public function look($t = 1)
    {
        $t--;
        $length = count($this->tokens);
        if ($this->cursor + $t > $length) $t = 0;
        if ($this->cursor + $t < 0) $t = -$this->cursor;
        return $this->tokens[$this->cursor + $t];
    }

    public function skip($times = 1)
    {
        for ($i = 0; $i < $times; $i++) {
            $this->next();
        }
        return $this;
    }

    public function expect($primary, $secondary = null)
    {
        $token = $this->getCurrentToken();
        if (is_null($secondary) && !is_int($primary)) {
            $secondary = $primary;
            $primary = Token::NAME_TYPE;
        }
        if (!$token->test($primary, $secondary)) {
            if (is_null($secondary)) {
                $expecting = Token::getTypeError($primary);
            } elseif (is_array($secondary)) {
                $expecting = '"' . implode('" or "', $secondary) . '"';
            } else {
                $expecting = '"' . $secondary . '"';
            }
            if ($token->getType() === Token::EOF_TYPE) {
                throw new SyntaxError('unexpected end of file', $token);
            } else {
                throw new SyntaxError(
                    sprintf(
                        'unexpected "%s", expecting %s',
                        str_replace("\n", '\n', $token->getValue()), $expecting
                    ),
                    $token
                );
            }
        }
        $this->next();
        return $token;
    }

    public function expectTokens($tokens)
    {
        foreach ($tokens as $token) {
            $this->expect($token->getType(), $token->getValue());
        }
        return $this;
    }

    public function test($primary, $secondary = null)
    {
        return $this->getCurrentToken()->test($primary, $secondary);
    }

    public function consume($primary, $secondary = null)
    {
        if ($this->test($primary, $secondary)) {
            $this->expect($primary, $secondary);
            return true;
        } else {
            return false;
        }
    }

    public function isEOS()
    {
        return $this->eos;
    }

    public function getCurrentToken()
    {
        return $this->currentToken;
    }

    public function getTokens()
    {
        return $this->tokens;
    }
}

class Token
{
    protected $type;
    protected $value;
    protected $line;
    protected $char;

    const EOF_TYPE          = -1;
    const TEXT_TYPE         = 0;
    const BLOCK_START_TYPE  = 1;
    const OUTPUT_START_TYPE = 2;
    const BLOCK_END_TYPE    = 3;
    const OUTPUT_END_TYPE   = 4;
    const NAME_TYPE         = 5;
    const NUMBER_TYPE       = 6;
    const STRING_TYPE       = 7;
    const OPERATOR_TYPE     = 8;
    const CONSTANT_TYPE     = 9;

    public function __construct($type, $value, $line, $char)
    {
        $this->type  = $type;
        $this->value = $value;
        $this->line  = $line;
        $this->char  = $char;
    }

    public static function getTypeAsString($type, $canonical = false)
    {
        if (is_string($type)) {
            return $canonical ? (__CLASS__ . '::' . $type) : $type;
        }

        switch ($type) {
        case self::EOF_TYPE:
            $name = 'EOF_TYPE';
            break;
        case self::TEXT_TYPE:
            $name = 'TEXT_TYPE';
            break;
        case self::BLOCK_START_TYPE:
            $name = 'BLOCK_START_TYPE';
            break;
        case self::OUTPUT_START_TYPE:
            $name = 'OUTPUT_START_TYPE';
            break;
        case self::BLOCK_END_TYPE:
            $name = 'BLOCK_END_TYPE';
            break;
        case self::OUTPUT_END_TYPE:
            $name = 'OUTPUT_END_TYPE';
            break;
        case self::NAME_TYPE:
            $name = 'NAME_TYPE';
            break;
        case self::NUMBER_TYPE:
            $name = 'NUMBER_TYPE';
            break;
        case self::STRING_TYPE:
            $name = 'STRING_TYPE';
            break;
        case self::OPERATOR_TYPE:
            $name = 'OPERATOR_TYPE';
            break;
        case self::CONSTANT_TYPE:
            $name = 'CONSTANT_TYPE';
            break;
        }
        return $canonical ? (__CLASS__ . '::' . $name) : $name;
    }

    public static function getTypeError($type)
    {
        switch ($type) {
        case self::EOF_TYPE:
            $name = 'end of file';
            break;
        case self::TEXT_TYPE:
            $name = 'text type';
            break;
        case self::BLOCK_START_TYPE:
            $name = 'block start (either "{%" or "{%-")';
            break;
        case self::OUTPUT_START_TYPE:
            $name = 'block start (either "{{" or "{{-")';
            break;
        case self::BLOCK_END_TYPE:
            $name = 'block end (either "%}" or "-%}")';
            break;
        case self::OUTPUT_END_TYPE:
            $name = 'block end (either "}}" or "-}}")';
            break;
        case self::NAME_TYPE:
            $name = 'name type';
            break;
        case self::NUMBER_TYPE:
            $name = 'number type';
            break;
        case self::STRING_TYPE:
            $name = 'string type';
            break;
        case self::OPERATOR_TYPE:
            $name = 'operator type';
            break;
        case self::CONSTANT_TYPE:
            $name = 'constant type (true, false, or null)';
            break;
        }
        return $name;
    }

    public function test($type, $values = null)
    {
        if (is_null($values) && !is_int($type)) {
            $values = $type;
            $type = self::NAME_TYPE;
        }

        return ($this->type === $type) && (
            is_null($values) ||
            (is_array($values) && in_array($this->value, $values)) ||
            $this->value == $values
        );
    }

    public function getType($asString = false, $canonical = false)
    {
        if ($asString) {
            return self::getTypeAsString($this->type, $canonical);
        }
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getChar()
    {
        return $this->char;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public static function tokenEOF($value, $line, $char)
    {
        return new self(self::EOF_TYPE, $value, $line, $char);
    }

    public static function tokenText($value, $line, $char)
    {
        return new self(self::TEXT_TYPE, $value, $line, $char);
    }

    public static function tokenBlockStart($value, $line, $char)
    {
        return new self(self::BLOCK_START_TYPE, $value, $line, $char);
    }

    public static function tokenOutputStart($value, $line, $char)
    {
        return new self(self::OUTPUT_START_TYPE, $value, $line, $char);
    }

    public static function tokenBlockEnd($value, $line, $char)
    {
        return new self(self::BLOCK_END_TYPE, $value, $line, $char);
    }

    public static function tokenOutputEnd($value, $line, $char)
    {
        return new self(self::OUTPUT_END_TYPE, $value, $line, $char);
    }

    public static function tokenName($value, $line, $char)
    {
        return new self(self::NAME_TYPE, $value, $line, $char);
    }

    public static function tokenNumber($value, $line, $char)
    {
        return new self(self::NUMBER_TYPE, $value, $line, $char);
    }

    public static function tokenString($value, $line, $char)
    {
        return new self(self::STRING_TYPE, $value, $line, $char);
    }

    public static function tokenOperator($value, $line, $char)
    {
        return new self(self::OPERATOR_TYPE, $value, $line, $char);
    }

    public static function tokenConstant($value, $line, $char)
    {
        return new self(self::CONSTANT_TYPE, $value, $line, $char);
    }
}


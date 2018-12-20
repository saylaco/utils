<?php

namespace Sayla\Util;

use Sayla\Exception\Error;
use Sayla\Helper\Data\Contract\DecoratesAccessibleArray;

class Evaluator implements \ArrayAccess
{
    use DecoratesAccessibleArray;
    protected $expression;
    protected $returnsValue = true;
    protected $endStatement = true;
    protected $properties;

    /**
     * @param string $expression
     * @param array $properties
     */
    public function __construct(string $expression, array $properties = [])
    {
        $this->expression = $expression;
        $this->properties = new \Sayla\Helper\Data\ArrayObject($properties);
    }

    /**
     * @param string $expression
     * @param mixed[] $vars
     * @return mixed
     */
    public static function toEval($expression, $vars = [])
    {
        return self::make($expression, $vars)->evaluate();
    }

    /**
     * @return mixed
     * @throws \Sayla\Exception\Error
     */
    public function evaluate()
    {
        $expression = $this->getExpression();
        try {
            return $this->doEvaluation($expression, $this->properties->toArray());
        } catch (\Throwable $exception) {
            throw (new Error('Failed to evaluate ' . var_export($expression, true), $exception))
                ->withExtra($exception->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->prepareExpression($this->expression);
    }

    /**
     * @param $expression
     * @return string
     */
    private function prepareExpression($expression)
    {
        if ($this->returnsValue) {
            if (!starts_with($expression, 'return ')) {
                $expression = 'return ' . $expression;
            }
        }
        return ($this->endStatement) ? str_finish($expression, ';') : $expression;
    }

    /**
     * @param string $expression
     * @return mixed
     */
    private function doEvaluation(string $expression, array $properties)
    {
        extract($properties, EXTR_PREFIX_INVALID & EXTR_PREFIX_SAME, 'evald');
        $result = eval($expression);
        return $result;
    }

    /**
     * @param string $expression
     * @param mixed[] $vars
     * @return self
     */
    public static function make($expression, $vars = [])
    {
        return new static($expression, $vars);
    }

    public function __toString()
    {
        return $this->getExpression();
    }

    /**
     * @param bool $shouldEndStatement
     * @return $this
     */
    public function endStatement(bool $shouldEndStatement = true)
    {
        $this->endStatement = $shouldEndStatement;
        return $this;

    }

    protected function getDecoratedArray(): \ArrayAccess
    {
        return $this->properties;
    }

    /**
     * @param bool|true $shouldPrependReturn
     * @return $this
     */
    public function returnValue(bool $shouldPrependReturn = true)
    {
        $this->returnsValue = $shouldPrependReturn;
        return $this;

    }
}
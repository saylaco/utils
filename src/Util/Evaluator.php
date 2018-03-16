<?php
namespace Sayla\Util;

use Sayla\Exception\Error;
use Sayla\Helper\Data\ArrayObject;

class Evaluator extends ArrayObject
{
    protected $expression;
    protected $returnsValue = true;
    protected $endStatement = true;

    /**
     * @param string $expression
     * @param array $properties
     */
    public function __construct(string $expression, array $properties = [])
    {
        $this->expression = $expression;
        parent::__construct($properties);
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
            extract($this->toArray(), EXTR_PREFIX_INVALID & EXTR_PREFIX_SAME, 'evald');
            return eval($expression);
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
<?php
/**
 * class CInteger wraper for GMP
 * Also it implements ISingle
 * @author Danil Volkov <volkovdanil91@gmail.com>
 * @package phpMath
 */
class CInteger implements ISingle {
    /**
     * Value of this integer
     * @var integer
     */
    protected $_value = null;

    /**
     * Private constructor for this integer, use CInteger::create
     * @param mixed $value
     */
    protected function __construct($value) {
        $this->_value = $value;
    }

    /**
     * Magic method that creates copy of current object
     */
    public function __clone() {}

    /**
     * Magic metod for string representation
     * @return string representation
     */
    public function __toString() {
        return (string)$this->_value;
    }

    /**
     * Returns value in php build-in classes
     * @return integer Value
     */
    public function getVal() {
        return $this->_value;
    }

    /**
     * Factory constructor
     * @param mixed $value
     * @return self
     */
    public static function create($value = 0) {
        if ($value instanceof self) {
            $initedValue = $value->_value;
        } elseif (is_string($value) || is_numeric($value)) {
            $initedValue = (int)$value;
        } else {
            $initedValue = 0;
        }
        return new self($initedValue);
    }

    /**
     * Addition of self objects
     * @param self $a
     * @param self $b
     * @return self return sum
     */
    public static function add(self $a, self $b) {
        return self::create($a->_value + $b->_value);
    }

    /**
     * Substruction of self objects
     * @param self $a
     * @param self $b
     * @return self return difference
     */
    public static function sub(self $a, self $b) {
        return self::create($a->_value - $b->_value);
    }

    /**
     * Multiplication of self objects
     * @param self $a
     * @param self $b
     * @return self return product
     */
    public static function mul(self $a, self $b) {
        return self::create($a->_value * $b->_value);
    }

    /**
     * Division of self objects
     * @param self $a
     * @param self $b
     * @return self return quotient
     */
    public static function div(self $a, self $b) {
        if (self::isZero($b)) {
            throw new Exception('Class ' . __CLASS__ . ': Division by zero!');
        }
        return self::create((int)($a->_value / $b->_value));
    }

    /**
     * Returns greatest common divisor
     * @param self $a
     * @param self $b
     * @return self return greatest common divisor
     */
    public static function gcd(self $a, self $b) {
        if (!self::isZero($a) && !self::isZero($b)) {
            $mx = max(abs($a->_value), abs($b->_value));
            $mn = min(abs($a->_value), abs($b->_value));

            while($mn != 0) {
                $tmp = $mn;
                $mn = $mx % $mn;
                $mx = $tmp;
            }
            return self::create($mx);
        }
        return self::create(1);
    }

    /**
     * Test is $a equal zero
     * @param self $a
     * @return boolean true if it is equal zero and false if not
     */
    public static function isZero(self $a) {
        return (0 == $a->_value);
    }

    /**
     * Test is $a equal one
     * @param self $a
     * @return boolean true if it is equal one and false if not
     */
    public static function isOne(self $a) {
        return (1 == $a->_value);
    }

    /**
     * Change sign to opposite ( mul by -1)
     * @param self $a
     * @return self return $a with opposite sign
     */
    public static function neg(self $a) {
        return self::create(-$a->_value);
    }

    /**
     * Is this number negative
     * @param self $a
     * @return boolean true if it is less than 0
     */
    public static function isNeg(self $a) {
        return ($a->_value < 0);
    }

    /**
     * Compare params
     * @param self $a
     * @param self $b
     * @return 0 if equal, 1 if $a greater, -1 if $b greater
     */
    public static function cmp(self $a, self $b) {
        return ($a->_value == $b->_value) ? 0 : (($a->_value - $b->_value) / abs($a->_value - $b->_value));
    }

    /**
     * Returns absolute value of $a
     * @param self $a
     * @return self absolute value of $a
     */
    public static function abs(self $a) {
        return self::create(abs($a->_value));
    }
}
?>

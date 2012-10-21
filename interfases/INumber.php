<?php
/**
 * Interface for Number
 * @author Danil Volkov <volkovdanil91@gmail.com>
 * @package phpMath
 */
interface INumber {

    /**
     * Magic method that creates copy of current object
     */
    public function __clone();

    /**
     * Magic metod for string representation
     * @return string representation
     */
    public function __toString();

    /**
     * Returns value in php build-in classes
     * @return mixed value
     */
    public function getVal();

    /**
     * Addition of self objects
     * @param self $a
     * @param self $b
     * @return self return sum
     */
    public static function add(self $a, self $b);

    /**
     * Substruction of self objects
     * @param self $a
     * @param self $b
     * @return self return difference
     */
    public static function sub(self $a, self $b);

    /**
     * Multiplication of self objects
     * @param self $a
     * @param self $b
     * @return self return product
     */
    public static function mul(self $a, self $b);

    /**
     * Division of self objects
     * @param self $a
     * @param self $b
     * @return self return quotient
     */
    public static function div(self $a, self $b);

    /**
     * Returns greatest common divisor
     * @param self $a
     * @param self $b
     * @return self return greatest common divisor
     */
    public static function gcd(self $a, self $b);

    /**
     * Test is $a equal zero
     * @param self $a
     * @return boolean true if it is equal zero and false if not
     */
    public static function isZero(self $a);

    /**
     * Test is $a equal one
     * @param self $a
     * @return boolean true if it is equal one and false if not
     */
    public static function isOne(self $a);

    /**
     * Change sign to opposite ( mul by -1)
     * @param self $a
     * @return self return $a with opposite sign
     */
    public static function neg(self $a);

    /**
     * Is this number negative
     * @param self $a
     * @return boolean true if it is less than 0
     */
    public static function isNeg(self $a);

    /**
     * Compare params
     * @param self $a
     * @param self $b
     * @return 0 if equal, 1 if $a greater, -1 if $b greater
     */
    public static function cmp(self $a, self $b);

    /**
     * Returns absolute value of $a
     * @param self $a
     * @return self absolute value of $a
     */
    public static function abs(self $a);

}
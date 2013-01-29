<?php
/**
 * class CFraction for fractions
 * Also it implements IComposite
 * @author Danil Volkov <volkovdanil91@gmail.com>
 * @package phpMath
 */
class CFraction implements IComposite {

    /**
     * Separator for hierarhy string
     */
    const HIERARHY_SEPARATOR = '.';

    /**
     * Precission of string and build-in representation
     * @var integer
     */
    protected static $_precision = 3;

    /**
     * Length after than we can delete some parts from Fraction
     * if 0 than no deletion
     * @var integer
     */
    protected static $_infelicity = 0;

    /**
     * How to show as string - decimal or standart
     * @var boolean
     */
    protected static $_decimal = false;

    /**
     * Numerator of the fraction
     * @var INumber
     */
    protected $_numerator = null;

    /**
     * Denominator of the fraction
     * @var INumber
     */
    protected $_denominator = null;

    /**
     * Class name for the Fraction Numerator and Denominator
     * @var string
     */
    protected $_numberClass = null;

    /**
     * Hierarhy of classes that created
     * @var string
     */
    protected $_hierarchy = null;


    /**
     * Private constructor, use CFraction::create
     * @param array $hierarchy
     * @param INumber $numerator
     * @param INumber $denominator
     */
    protected function __construct($hierarchy, $numerator, $denominator) {
        $this->_numberClass = reset($hierarchy);
        $this->_hierarchy = implode(self::HIERARHY_SEPARATOR, $hierarchy);
        $this->_numerator = $numerator;
        $this->_denominator = $denominator;
    }

    /**
     * Clone this fraction
     */
    public function __clone() {
        $this->_numerator = clone $this->_numerator;
        $this->_denominator = clone $this->_denominator;
    }

    /**
     * Show as decimal fraction
     * @return string
     */
    protected function _asFloatStr() {
        $numberClass = $this->_numberClass;
        $hierarhy = $this->getSubHierarchy();

        if (empty($hierarhy)) {
            $multiplier = $numberClass::create(10);
        } else {
            $multiplier = $numberClass::create($hierarhy, 10);
        }

        $value = clone $this->_numerator;
        for($i = 0; $i <= self::$_precision; ++$i) {
            $value = $numberClass::mul($multiplier, $value);
        }

        $res = $numberClass::div($value, $this->_denominator);
        $str = (string)$numberClass::abs($res);

        $position = strlen($str) - self::$_precision - 1;

        if ($position > 0) {
            $str = substr($str, 0, $position) . '.' . substr($str, $position);
        } elseif ($position <= 0) {
            $str = '0.' . str_pad('', -$position, '0', STR_PAD_LEFT) . $str;
        }

        return ($numberClass::isNeg($res) ? '-' : '') . rtrim(rtrim($str, '0'), '.');
    }

    /**
     * String representation
     * @return string
     */
    public function __toString() {
        $numberClass = $this->_numberClass;
        if ($numberClass::isOne($this->_denominator)) {
            return (string)$this->_numerator;
        } elseif ($numberClass::isZero($this->_denominator)) {
            return 'inf';
        } elseif (self::$_decimal) {
            return $this->_asFloatStr();
        } else {
            return (string)($this->_numerator . '/' . $this->_denominator . ' ');
        }
    }

    /**
     * Returns val in php build-in classes
     * @return float Value
     */
    public function getVal() {
        $numberClass = $this->_numberClass;
        if ($numberClass::isZero($this->_denominator)) {
            return INF;
        }
        return (float)$this->_asFloatStr();
    }

    /**
     * Returns hierarchy of this object
     * @return string
     */
    public function getHierarchy() {
        return $this->_hierarchy;
    }

    /**
     * Returns element of fraction hierarchy
     * @param type $hierarhy
     * @param type $level
     * @return string
     */
    public function getSubHierarchy($hierarhy = '', $level = 1) {
        if (empty($hierarhy)) {
            $hierarhy = $this->_hierarchy;
        }
        $hierarhy = array_slice(explode(self::HIERARHY_SEPARATOR, $hierarhy), $level);
        return implode(self::HIERARHY_SEPARATOR, $hierarhy);
    }

    /**
     * Compares hierarhy of two strings
     * @param IComposite|string $a
     * @param IComposite|string $b
     * @return boolean
     */
    public static function cmpHierarchy($a, $b) {
        if (is_string($a)) {
            $aHierarhy = $a;
        } elseif ($a instanceof IComposite) {
            $aHierarhy = $a->getHierarchy();
        } else {
            $aHierarhy = '';
        }

        if (is_string($b))  {
            $bHierarhy = $b;
        } elseif ($b instanceof IComposite) {
            $bHierarhy = $b->getHierarchy();
        } else {
            $bHierarhy = '';
        }

        return ($aHierarhy === $bHierarhy);
    }

    /**
     * Factory constructor
     * @param string|array $hierarchy must implement INumber
     * @param array $values
     * @return self
     * @throws Exception if class incorrect ot do not implement INumber
     */
    public static function create($hierarchy, $values) {
        if (is_string($hierarchy)) {
            $hierarchy = explode(self::HIERARHY_SEPARATOR, $hierarchy);
        }
        if (!is_array($hierarchy)) {
            throw new Exception('Incorrect hierarchy "' . var_export($hierarchy, true) . '"! It must be string or array' . "\n");
        }
        $subClasses = array_slice($hierarchy, 1);
        $numberClass = reset($hierarchy);

        $numerator = 0;
        $denominator = 1;
        if (is_array($values)) {
            foreach(array(0, 'n', 'numerator') as $key) {
                if (isset($values[$key])) {
                    $numerator = $values[$key];
                }
            }
            foreach(array(1, 'd', 'denominator') as $key) {
                if (isset($values[$key])) {
                    $denominator = $values[$key];
                }
            }
        } else {
            $numerator = $values;
        }


        if (
            ($numerator instanceof self) &&
            (self::cmpHierarchy($numerator, implode(self::HIERARHY_SEPARATOR, $hierarchy)))
        ) {
            return clone $numerator;
        } elseif (class_exists($numberClass)) {
            $interfases = class_implements($numberClass);
            if (isset($interfases['ISingle']) || isset($interfases['IComposite'])) {
                //For numerator
                if (!($numerator instanceof $numberClass)) {
                    if (empty($subClasses)) {
                        $numerator = $numberClass::create($numerator);
                    } else {
                        $numerator = $numberClass::create($subClasses, $numerator);
                    }
                } elseif (!self::cmpHierarchy($numerator, implode(self::HIERARHY_SEPARATOR, $subClasses))) {
                    throw new Exception('Numerator object incorrect!');
                }
                //For denumerator
                if (!($denominator instanceof $numberClass)) {
                    if (empty($subClasses)) {
                        $denominator = $numberClass::create($denominator);
                    } else {
                        $denominator = $numberClass::create($subClasses, $denominator);
                    }
                } elseif (!self::cmpHierarchy($denominator, implode(self::HIERARHY_SEPARATOR, $subClasses))) {
                    throw new Exception('Denominator object incorrect!');
                }

                return self::reduce(new self($hierarchy, $numerator, $denominator));
            }
        }
        throw new Exception('Incorrect class "' . $numberClass . '"! It must exists and implemets INumber' . "\n");
    }

    /**
     * Make fraction shorter,
     * but with computing erros
     * see self::$_infelicity
     * @param self $a
     * @return self
     */
    protected static function makeShorter(self $a) {
        if ((0 == self::$_infelicity) ||
            (strlen($a->_denominator) < self::$_infelicity)) {
            return $a;
        }
        $numberClass = $a->_numberClass;
        $hierarchy = $a->getSubHierarchy();
        if (empty($hierarchy)) {
            $ten = $numberClass::create(10);
            $multiplier = $numberClass::create(1);
        } else {
            $ten = $numberClass::create($hierarchy, 10);
            $multiplier = $numberClass::create($hierarchy, 1);
        }

        $len = self::$_infelicity;

        for($i = 0; $i < $len; ++$i) {
            $multiplier = $numberClass::mul($multiplier, $ten);
        }

        $a->_numerator = $numberClass::div(
            $numberClass::mul(
                $a->_numerator,
                $multiplier
            ),
            $a->_denominator
        );

        if ($numberClass::isZero($a->_numerator)) {
            if (empty($hierarchy)) {
                $a->_numerator = $numberClass::create(0);
                $a->_denominator = $numberClass::create(1);
            } else {
                $a->_numerator = $numberClass::create($hierarchy, 0);
                $a->_denominator = $numberClass::create($hierarchy, 1);
            }
        } else {
            $a->_denominator = $multiplier;
        }

        return $a;
    }

    /**
     * Returns reducing $a
     * @param self $a
     * @return self
     */
    protected static function reduce(self $a) {
        $numberClass = $a->_numberClass;
        $gcd = $numberClass::gcd($a->_numerator, $a->_denominator);

        if (!$numberClass::isZero($gcd)) {
            $a->_numerator = $numberClass::div($a->_numerator, $gcd);
            $a->_denominator = $numberClass::div($a->_denominator, $gcd);
        }

        if ($numberClass::isNeg($a->_denominator)) {
            $a->_numerator = $numberClass::neg($a->_numerator);
            $a->_denominator = $numberClass::neg($a->_denominator);
        }
        return self::makeShorter($a);
    }

    /**
     * Addition of self objects
     * @param self $a
     * @param self $b
     * @return self return sum
     */
    public static function add(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;
            $numerator = $numberClass::add(
                $numberClass::mul($a->_numerator,$b->_denominator),
                $numberClass::mul($b->_numerator, $a->_denominator)
            );
            $denominator = $numberClass::mul($a->_denominator, $b->_denominator);
            return self::create($a->_hierarchy, array('n' => $numerator, 'd' => $denominator));
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Substruction of self objects
     * @param self $a
     * @param self $b
     * @return self return difference
     */
    public static function sub(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;
            $numerator = $numberClass::sub(
                $numberClass::mul($a->_numerator,$b->_denominator),
                $numberClass::mul($b->_numerator, $a->_denominator)
            );
            $denominator = $numberClass::mul($a->_denominator, $b->_denominator);
            return self::create($a->_hierarchy, array('n' => $numerator, 'd' => $denominator));
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Multiplication of self objects
     * @param self $a
     * @param self $b
     * @return self return product
     */
    public static function mul(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;
            $numerator = $numberClass::mul($a->_numerator,$b->_numerator);
            $denominator = $numberClass::mul($b->_denominator, $a->_denominator);
            return self::create($a->_hierarchy, array('n' => $numerator, 'd' => $denominator));
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Division of self objects
     * @param self $a
     * @param self $b
     * @return self return quotient
     */
    public static function div(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;
            $numerator = $numberClass::mul($a->_numerator,$b->_denominator);
            $denominator = $numberClass::mul($b->_numerator, $a->_denominator);
            return self::create($a->_hierarchy, array('n' => $numerator, 'd' => $denominator));
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Returns greatest common divisor
     * @param self $a
     * @param self $b
     * @return self return greatest common divisor
     */
    public static function gcd(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;
            $numerator = $numberClass::gcd($a->_numerator, $b->_numerator);
            $denominator = $numberClass::gcd($a->_denominator, $b->_denominator);
            return self::create($a->_hierarchy, array('n' => $numerator, 'd' => $denominator));
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Test is $a equal zero
     * @param self $a
     * @return boolean true if it is equal zero and false if not
     */
    public static function isZero(self $a) {
        $numberClass = $a->_numberClass;
        return $numberClass::isZero($a->_numerator);
    }

    /**
     * Test is $a equal one
     * @param self $a
     * @return boolean true if it is equal one and false if not
     */
    public static function isOne(self $a) {
        $numberClass = $a->_numberClass;
        return ($numberClass::isOne($a->_numerator) && $numberClass::isOne($a->_denominator));
    }

    /**
     * Change sign to opposite ( mul by -1)
     * @param self $a
     * @return self return $a with opposite sign
     */
    public static function neg(self $a) {
        $numberClass = $a->_numberClass;
        $ret = clone $a;
        $ret->_numerator = $numberClass::neg($ret->_numerator);
        return $ret;
    }

    /**
     * Is this number negative
     * @param self $a
     * @return boolean true if it is less than 0
     */
    public static function isNeg(self $a) {
        $numberClass = $a->_numberClass;
        return $numberClass::isNeg($a->_numerator);
    }

    /**
     * Compare params
     * @param self $a
     * @param self $b
     * @return 0 if equal, 1 if $a greater, -1 if $b greater
     */
    public static function cmp(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;

            return $numberClass::cmp(
                $numberClass::mul($a->_numerator, $b->_denominator),
                $numberClass::mul($a->_denominator, $b->_numerator)
            );
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Returns absolute value of $a
     * @param self $a
     * @return self absolute value of $a
     */
    public static function abs(self $a) {
        $numberClass = $a->_numberClass;
        $ret = clone $a;
        $ret->_numerator = $numberClass::abs($ret->_numerator);
        $ret->_denominator = $numberClass::abs($ret->_denominator);
        return $ret;
    }


    /**
     * Gets current precision
     * @return integer
     */
    public static function getPrecision() {
        return self::$_precision;
    }

    /**
     * Sets precission
     * @param integer $precision
     * @return boolean success or not
     */
    public static function setPrecision($precision) {
        if (is_int($precision) && ($precision >= 0)) {
            self::$_precision = $precision;
            return true;
        }
        return false;
    }

    /**
     * Set type of view decimal or standart (true/false)
     * @param boolean $decimal
     */
    public static function setView($decimal) {
        self::$_decimal = (bool)$decimal;
    }

}
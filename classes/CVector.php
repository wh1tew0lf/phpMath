<?php
/**
 * class CVector for vectors
 * Also it implements IComposite
 * @author Danil Volkov <volkovdanil91@gmail.com>
 * @package phpMath
 */
class CVector implements IComposite {
    /**
     * Separator for hierarhy string
     */
    const HIERARHY_SEPARATOR = '.';

    /**
     * Class name for the Vector elements
     * @var string
     */
    protected $_numberClass = null;

    /**
     * Elements of the vector
     * @var array of INumbers
     */
    protected $_vector = null;

    /**
     * Separator of vector elements in string representation
     * @var string
     */
    protected $_separator = ' ';

    /**
     * Hierarhy of classes that created
     * @var string
     */
    protected $_hierarchy = null;

    /**
     * Private constructor, see self::create
     * @param array $hierarchy
     * @param array of INumbers $vector
     * @param string $separator
     */
    protected function __construct($hierarchy, $vector, $separator) {
        $this->_numberClass = reset($hierarchy);
        $this->_hierarchy = implode(self::HIERARHY_SEPARATOR, $hierarchy);
        $this->_vector = $vector;
        $this->_separator = $separator;
    }

    /**
     * Clone this object
     * @return self
     */
    public function __clone() {
        $size = $this->getSize();
        for($i = 0; $i < $size; ++$i) {
            $this->_vector[$i] = clone $this->_vector[$i];
        }
    }

    /**
     * String representation
     * @return string
     */
    public function __toString() {
        return implode($this->_separator, $this->_vector);
    }

    /**
     * Returns sum of the elements
     * @return mixed Value
     */
    public function getVal() {
        $numberClass = $this->_numberClass;
        $hierarhy = $this->getSubHierarchy();
        if (empty($hierarhy)) {
            $sum = $numberClass::create(0);
        } else {
            $sum = $numberClass::create($hierarhy, 0);
        }
        $size = $this->getSize();
        for($i = 0; $i < $size; ++$i) {
            $sum = $numberClass::add($sum, $this->_vector[$i]);
        }
        return $sum->getVal();
    }

    /**
     * Return hierarchy of this object
     * @return string
     */
    public function getHierarchy() {
        return $this->_hierarchy;
    }

    /**
     * Returns element hierarchy
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
     * Returns count of the vector elements
     * @return integer
     */
    public function getSize() {
        if (is_array($this->_vector)) {
            return count($this->_vector);
        }
        return 0;
    }

    /**
     * Returns element at $i index
     * @param integer $i index
     * @return INumber
     */
    public function getAt($i) {
        if (($i < $this->getSize()) && isset($this->_vector[$i])) {
            return $this->_vector[$i];
        }
        $numberClass = $this->_numberClass;
        $hierarhy = $this->getSubHierarchy();
        if (empty($hierarhy)) {
            return $numberClass::create(0);
        } else {
            return $numberClass::create($hierarhy, 0);
        }
    }

    /**
     * Sets element at $i index
     * @param integer $i index
     * @param INumber $value
     * @return boolean success or not
     */
    public function setAt($i, $value) {
        if (($i > $this->getSize()) || !isset($this->_vector[$i])) {
            return false;
        }

        $numberClass = $this->_numberClass;
        $hierarchy = $this->getSubHierarchy();
        if (!self::cmpHierarchy($value, $hierarchy) || (get_class($value) != $numberClass)) {
            if (empty($hierarchy)) {
                $value = $numberClass::create($value);
            } else {
                $value = $numberClass::create($hierarchy, $value);
            }
        }

        $this->_vector[$i] = $value;
        return true;
    }

    /**
     * Remove element at index $i and return it
     * @param integer $i index
     * @return null|INumber if no element return null
     */
    public function remove($i) {
        if (($i > $this->getSize()) || !isset($this->_vector[$i])) {
            return null;
        }

        $ret = $this->_vector[$i];
        $this->_vector[$i] = null;
        unset($this->_vector[$i]);
        $this->_vector = array_merge($this->_vector);
        return $ret;
    }

    /**
     * Insert element at $i index
     * @param integer $i index
     * @param INumber $value inserted value
     */
    public function insert($i, $value) {
        $numberClass = $this->_numberClass;
        $hierarchy = $this->getSubHierarchy();
        if (!self::cmpHierarchy($value, $hierarchy) || (get_class($value) != $numberClass)) {
            if (empty($hierarchy)) {
                $value = $numberClass::create($value);
            } else {
                $value = $numberClass::create($hierarchy, $value);
            }
        }

        if (($i >= 0) && ($i < $this->getSize())) {
            $before = array_slice($this->_vector, 0, $i);
            $after = array_slice($this->_vector, $i);
            $this->_vector = array_merge($before,array($value),$after);
        } elseif ($i <= 0) {
            $this->_vector = array_merge(array($value),$this->_vector);
        } else {
            $this->_vector[] = $value;
        }
    }

    /**
     * Swaps two elements
     * @param type $i
     * @param type $j
     * @return boolean Success or not
     */
    public function swap($i, $j) {
        if (($i > $this->getSize()) || !isset($this->_vector[$i])) {
            return false;
        }

        if (($j > $this->getSize()) || !isset($this->_vector[$j])) {
            return false;
        }
        $c = $this->_vector[$j];
        $this->_vector[$j] = $this->_vector[$i];
        $this->_vector[$i] = $c;
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
     * Creates new element os self
     * @param array|string $hierarchy
     * @param array $vector
     * @param string $separator
     * @return self
     * @throws Exception
     */
    public static function create($hierarchy, $vector, $separator = ' ') {
        if (is_string($hierarchy)) {
            $hierarchy = explode(self::HIERARHY_SEPARATOR, $hierarchy);
        }
        if (!is_array($hierarchy)) {
            throw new Exception('Incorrect hierarchy "' . var_export($hierarchy, true) . '"! It must be string or array' . "\n");
        }
        $subClasses = array_slice($hierarchy, 1);
        $numberClass = reset($hierarchy);

        if (class_exists($numberClass)) {
            $interfases = class_implements($numberClass);
            if (isset($interfases['ISingle']) || isset($interfases['IComposite'])) {
                $initedVector = array();
                if (is_array($vector)) {
                    foreach($vector as $k => $element) {
                        if (!($element instanceof $numberClass)) {
                            if (empty($subClasses)) {
                                $element = $numberClass::create($element);
                            } else {
                                $element = $numberClass::create($subClasses, $element);
                            }
                        } elseif (!self::cmpHierarchy($element, implode(self::HIERARHY_SEPARATOR, $subClasses))) {
                            throw new Exception('Element[' . $k . '] object incorrect!');
                        }
                        $initedVector[] = $element;
                    }
                }
                return new static($hierarchy, $initedVector, $separator);
            }
        }
        throw new Exception('Incorrect class "' . $numberClass . '"! It must exists and implemets INumber' . "\n");
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
            $size = max($a->getSize(), $b->getSize());
            $vector = array();
            for($i = 0; $i < $size; ++$i) {
                $vector[$i] = $numberClass::add($a->getAt($i), $b->getAt($i));
            }
            return self::create($a->_hierarchy, $vector, $a->_separator);
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
            $size = max($a->getSize(), $b->getSize());
            $vector = array();
            for($i = 0; $i < $size; ++$i) {
                $vector[$i] = $numberClass::sub($a->getAt($i), $b->getAt($i));
            }
            return self::create($a->_hierarchy, $vector, $a->_separator);
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Scalar multiplication of self objects
     * @param self $a
     * @param self $b
     * @return self return product
     */
    public static function mul(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;
            $size = max($a->getSize(), $b->getSize());
            $vector = array();
            for($i = 0; $i < $size; ++$i) {
                $vector[$i] = $numberClass::mul($a->getAt($i), $b->getAt($i));
            }
            return self::create($a->_hierarchy, $vector, $a->_separator);
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Scalar division of self objects
     * @param self $a
     * @param self $b
     * @return self return quotient
     */
    public static function div(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;
            $size = max($a->getSize(), $b->getSize());
            $vector = array();
            for($i = 0; $i < $size; ++$i) {
                $vector[$i] = $numberClass::div($a->getAt($i), $b->getAt($i));
            }
            return self::create($a->_hierarchy, $vector, $a->_separator);
        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Multiplication vector on scalar
     * @param self $v
     * @param mixed $a
     * @return self return product
     */
    public static function muls(self $v, $a) {
        $numberClass = $v->_numberClass;
        $hierarchy = $v->getSubHierarchy();
        if (!self::cmpHierarchy($a, $hierarchy) || (get_class($a) != $numberClass)) {
            if (empty($hierarchy)) {
                $a = $numberClass::create($a);
            } else {
                $a = $numberClass::create($hierarchy, $a);
            }
        }
        $size = $v->getSize();
        $vector = array();
        for($i = 0; $i < $size; ++$i) {
            $vector[$i] = $numberClass::mul($v->_vector[$i], $a);
        }
        return self::create($v->_hierarchy, $vector);
    }

    /**
     * Division vector on scalar
     * @param self $v
     * @param mixed $b
     * @return self return quotient
     */
    public static function divs(self $v, $a) {
        $numberClass = $v->_numberClass;
        $hierarchy = $v->getSubHierarchy();
        if (!self::cmpHierarchy($a, $hierarchy) || (get_class($a) != $numberClass)) {
            if (empty($hierarchy)) {
                $a = $numberClass::create($a);
            } else {
                $a = $numberClass::create($hierarchy, $a);
            }
        }
        $size = $v->getSize();
        $vector = array();
        for($i = 0; $i < $size; ++$i) {
            $vector[$i] = $numberClass::div($v->_vector[$i], $a);
        }
        return self::create($v->_hierarchy, $vector);
    }

    /**
     * Returns greatest common divisor
     * @param self $a
     * @param self $b
     * @return self return greatest common divisor
     */
    public static function gcd(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            if ($a->getSize() == $b->getSize()) {
                $numberClass = $a->_numberClass;
                $size = $a->getSize();
                $gcd = null;
                for($i = 0; $i < $size; ++$i) {
                    if (!$numberClass::isZero($a->getAt($i))) {
                        if (null === $gcd) {
                            $gcd = $a->getAt($i);
                        } else {
                            $gcd = $numberClass::gcd($gcd, $a->getAt($i));
                        }
                    }

                    if (!$numberClass::isZero($b->getAt($i))) {
                        if (null === $gcd) {
                            $gcd = $b->getAt($i);
                        } else {
                            $gcd = $numberClass::gcd($gcd, $b->getAt($i));
                        }
                    }

                    if ((null != $gcd) && $numberClass::isOne($gcd)) { break; }
                }

                if (null === $gcd) {
                    $hierarchy = $a->getSubHierarchy();
                    if (empty($hierarchy)) {
                        $a = $numberClass::create(1);
                    } else {
                        $a = $numberClass::create($hierarchy, 1);
                    }
                }

                return $gcd;
            }
            throw new Exception('Vectors must have same legth');

        }
        throw new Exception('Classes of a and b must be equal!');
    }

    /**
     * Test is $a equal zero
     * @param self $a
     * @return boolean true if it is equal zero and false if not
     */
    public static function isZero(self $a) {
        $answer = true;
        $numberClass = $a->_numberClass;
        $size = $a->getSize();
        for($i = 0; $i < $size; ++$i) {
            $answer = $answer && ($numberClass::isZero($a->_vector[$i]));
        }
        return $answer;
    }

    /**
     * Test is $a equal one
     * @param self $a
     * @return boolean true if it is equal one and false if not
     */
    public static function isOne(self $a) {
        $answer = true;
        $numberClass = $a->_numberClass;
        $size = $a->getSize();
        for($i = 0; $i < $size; ++$i) {
            $answer = $answer && ($numberClass::isOne($a->_vector[$i]));
        }
        return $answer;
    }

    /**
     * Change sign to opposite ( mul by -1)
     * @param self $a
     * @return self return $a with opposite sign
     */
    public static function neg(self $a) {
        $ret = clone $a;
        $numberClass = $ret->_numberClass;
        $size = $ret->getSize();
        for($i = 0; $i < $size; ++$i) {
            $ret->_vector[$i] = $numberClass::neg($ret->_vector[$i]);
        }
        return $ret;
    }

    /**
     * Is more elements of this vector is negative
     * @param self $a
     * @return boolean true if it is less than 0
     */
    public static function isNeg(self $a) {
        $count = 0;
        $numberClass = $a->_numberClass;
        $size = $a->getSize();
        for($i = 0; $i < $size; ++$i) {
            if ($numberClass::isNeg($a->_vector[$i])) {
                ++$count;
            }
        }
        return (($size / 2) < $count);
    }

    /**
     * Change separator str
     * @param string $separator
     * @return boolean changed or not
     */
    public function setSeparator($separator) {
        if (is_string($separator)) {
            $this->_separator = $separator;
            return true;
        }
        return false;
    }

    /**
     * Compare vectors lexicographically
     * @param self $a
     * @param self $b
     * @return 0 if equal, 1 if $a greater, -1 if $b greater
     */
    public static function cmp(self $a, self $b) {
        if (self::cmpHierarchy($a, $b)) {
            $numberClass = $a->_numberClass;
            $cmp = 0;
            $size = $a->getSize();
            for($i = 0; $i < $size; ++$i) {
                $cmp = $numberClass::cmp($a->_vector[$i],$b->_vector[$i]);
                if (0 != $cmp) { break; }
            }
            return $cmp;
        }
    }

    /**
     * Returns absolute value of $a
     * @param self $a
     * @return self absolute value of $a
     */
    public static function abs(self $a) {
        $ret = clone $a;
        $numberClass = $ret->_numberClass;
        $size = $ret->getSize();
        for($i = 0; $i < $size; ++$i) {
            $ret->_vector[$i] = $numberClass::abs($ret->_vector[$i]);
        }
        return $ret;
    }
}
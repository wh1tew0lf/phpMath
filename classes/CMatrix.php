<?php
/**
 * class CMatrix for matrix (vectors of vectors)
 * Also it implements IComposite
 * @author Danil Volkov <volkovdanil91@gmail.com>
 * @package phpMath
 */
class CMatrix extends CVector {

    /**
     * Creates new element os self
     * @param array|string $hierarchy
     * @param array $matrix
     * @param string $separator
     * @return self
     * @throws Exception
     */
    public static function create($hierarchy, $matrix, $separator = "\n") {
        $N = count($matrix);
        $M = count(reset($matrix));

        $vectors = array();
        for($i = 0; $i < $N; ++$i) {
            $count = count($matrix[$i]);
            if ($count < $M) {
                $matrix[$i] = array_pad($matrix[$i], $M, 0);
            } elseif ($count > $M) {
                $matrix[$i] = array_slice($matrix[$i], 0, $M);
            }

            $vectors[$i] = CVector::create($hierarchy, $matrix[$i]);
        }

        $hierarchy = 'CVector.' . $hierarchy;
        return parent::create($hierarchy, $vectors, $separator);
    }

    /**
     * Returns line at $i index
     * @param integer $i index
     * @return INumber
     */
    public function getLine($i) {
        return parent::getAt($i);
    }

    /**
     * Sets line at $i index
     * @param integer $i index
     * @param CVector $line
     * @return INumber
     */
    public function setLine($i, CVector $line) {
        return parent::setAt($i, $line);
    }

    /**
     * Removes line $i
     * @param integer $i
     * @return CVector
     */
    public function removeLine($i) {
        return parent::remove($i);
    }

    /**
     * Removes line $i
     * @param integer $i
     * @return boolean
     */
    public function insertLine($i, CVector $line) {
        return parent::insert($i, $line);
    }

    /**
     * Gets element at [$i, $j] or $i line
     * @param integer $i
     * @param integer|null $j
     * @return INumber
     */
    public function getAt($i, $j = null) {
        if (null === $j) {
            return parent::getAt($i);
        }
        if (($i < $this->getSize()) && isset($this->_vector[$i])) {
            return $this->_vector[$i]->getAt($j);
        }

        $hierarhy = $this->getSubHierarchy('', 2);
        $numberClass = reset(explode(self::HIERARHY_SEPARATOR,$hierarhy));
        if (empty($hierarhy)) {
            return $numberClass::create(0);
        } else {
            return $numberClass::create($hierarhy, 0);
        }
    }

    /**
     * Sets element at [$i, $j] or $i line
     * @param integer $i
     * @param integer|CVector $j
     * @param INumber|null $value
     * @return boolean
     */
    public function setAt($i, $j, $value = null) {
        if (null === $value) {
            return parent::setAt($i, $j);
        }

        if (($i >= $this->getSize()) || !isset($this->_vector[$i])) {
            return false;
        }
        return $this->_vector[$i]->setAt($j, $value);
    }

    /**
     * Returns height of the matrix
     * @return integer
     */
    public function getHeight() {
        return $this->getSize();
    }

    /**
     * Returns width of the matrix
     * @return integer
     */
    public function getWidth() {
        if ($this->getSize() != 0) {
            return $this->_vector[0]->getSize();
        }
        return 0;
    }

}
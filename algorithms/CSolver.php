<?php
/**
 * Class that realize some algorithm for matrices
 */
class CSolver {
    /**
     * Array of errors that arises on algorithm work
     * @var array
     */
    public static $errors = array();
    /**
     * Try to reduce lines
     * @var boolean
     */
    public static $lineOptimize = true;
    /**
     * Clone objects of algorithm, or change objects
     * @var boolean
     */
    public static $clone = true;
    /**
     * Temp matrix inits on some algorithm start,
     * you can use it for see what returned after algorithm
     * @var mixed
     */
    public static $matrix = null;
    /**
     * Temp vector inits on some algorithm start,
     * you can use it for see what returned after algorithm
     * @var mixed
     */
    public static $vector = null;

    // <editor-fold defaultstate="collapsed" desc="show iter function for some three algorithms">
    public static function showNyuton($i, $start, $f, $delta) {
        $str = "\n----- [" . $i . "] -----\n";
        $str = $str . 'F: ' . $f . "\n";
        $str = $str . 'iterVector: ' . $start . "\n";
        $str = $str . 'delta: ' . $delta . "\n";
        $str = $str . "----------------\n";
        echo $str;
    }

    public static function showGaussIter($i, $matrix, $vector) {
        $str = "\n----- [" . $i . "] -----\n";

        $H = $matrix->getHeight();
        for($i = 0; $i < $H; ++$i) {
            $str = $str . (string)$matrix->getLine($i) . ' = ' . (string)$vector->getAt($i) . "\n";
        }
        $str = $str . "----------------\n";

        echo $str;
    }

    public static function showGaussZeidelIter($i, $matrix, $vector, $start) {
        $str = "\n----- [" . $i . "] -----\n";

        $H = $matrix->getHeight();
        for($i = 0; $i < $H; ++$i) {
            $str = $str . (string)$matrix->getLine($i) . ' = ' . (string)$vector->getAt($i) . "\n";
        }
        $str = $str . 'vector X: ' . $start . "\n";
        $str = $str . "----------------\n";

        echo $str;
    }// </editor-fold>

    /**
     * Substitute the solution to the equations
     * @param CMatrix $matrix
     * @param CVector $vector
     * @param CVector $solution
     * @param type $pows if it is non-linear equations
     * @return CVector
     */
    public static function testSolution(CMatrix $matrix, CVector $vector, CVector $solution, $pows = null) {
        $N = $matrix->getHeight();
        $M = $matrix->getWidth();
        $h = $matrix->getSubHierarchy();
        $numberClass = reset(explode(CMatrix::HIERARHY_SEPARATOR, $h));
        $result = array();
        for($i = 0; $i < $N; ++$i) {
            $result[$i] = clone $vector->getAt($i);
            for($j = 0; $j < $M; ++$j) {
                $result[$i] = $numberClass::sub(
                    $result[$i],
                    $numberClass::mul(
                        $matrix->getAt($i,$j),
                        isset($pows[$i][$j]) ? self::pow($solution->getAt($j), $pows[$i][$j]) : $solution->getAt($j)
                    )
                );
            }
        }
        return CVector::create($h, $result);
    }

    /**
     * Gauth algorithm for solving linear equations
     * @param CMatrix $matrix
     * @param CVector $vector
     * @param type $show
     * @return boolean|CVector vector-solution if solved or false
     */
    public static function gauss(CMatrix $matrix, CVector $vector, $show = null) {
        self::$errors = array();
        self::$matrix = null;
        self::$vector = null;

        if (self::$clone) {
            $matrix = clone $matrix;
            $vector = clone $vector;
        }

        $N = $matrix->getHeight();
        $M = $matrix->getWidth();
        $L = $vector->getSize();
        if ($N != $L) {
            self::$errors[] = 'Size of matrix do not equal size of vector';
        }

        if ($N != $M) {
            self::$errors[] = 'Matrix is not square';
        }

        if ($matrix->getSubHierarchy() != $vector->getHierarchy()) {
            self::$errors[] = 'Matrix and vector elements do not same';
        }

        $h = $matrix->getSubHierarchy();
        $numberClass = reset(explode(CMatrix::HIERARHY_SEPARATOR, $h));


        $haveSolution = true;
        $size = min($N, $M, $L);

        if (null !== $show) {
            call_user_func_array($show, array('START', $matrix, $vector));
        }

        for($i = 0; $i < ($size - 1); ++$i) {
            $zero = $numberClass::isZero($matrix->getAt($i, $i));
            if ($zero) {
                for($l = ($i + 1); $l < $size; ++$l) {
                    if (!$numberClass::isZero($matrix->getAt($l, $i))) {
                        $matrix->swap($i, $l);
                        $vector->swap($i, $l);
                        $zero = false;
                        break;
                    }
                }
            }
            $haveSolution = $haveSolution && !$zero;
            if (!$zero) {

                if (self::$lineOptimize) {
                    for($l = ($i + 1); $l < $size; ++$l) {
                        $matrix->setAt($i, $l,
                            $numberClass::div($matrix->getAt($i, $l),$matrix->getAt($i, $i))
                        );
                    }
                    $vector->setAt($i,$numberClass::div($vector->getAt($i),$matrix->getAt($i, $i)));
                    $matrix->setAt($i, $i, 1);
                }

                for($j = ($i + 1); $j < $size; ++$j) {
                    $d = $matrix->getAt($i, $i);
                    $c = $matrix->getAt($j, $i);
                    $matrix->setAt($j, $i, 0);
                    for ($k = ($i + 1); $k < $size; ++$k) {
                        $matrix->setAt($j, $k,
                            $numberClass::sub(
                                $numberClass::mul($matrix->getAt($j, $k), $d),
                                $numberClass::mul($matrix->getAt($i, $k), $c)
                            )
                        );
                    }
                    $vector->setAt($j, $numberClass::sub(
                        $numberClass::mul($vector->getAt($j), $d),
                        $numberClass::mul($vector->getAt($i), $c)
                    ));
                }
            } else {
                self::$errors[] = 'The [' . $i . '] column empty';
            }
            if (null !== $show) {
                call_user_func_array($show, array($i, $matrix, $vector));
            }
        }

        $haveSolution = $haveSolution && !$numberClass::isZero($matrix->getAt($size - 1, $size - 1));

        if ($haveSolution) {
            for ($i = ($size - 1) ; $i > 0; --$i) {

                if (self::$lineOptimize) {
                    for($l = ($i + 1); $l < $size; ++$l) {
                        $matrix->setAt($i, $l,
                            $numberClass::div($matrix->getAt($i, $l),$matrix->getAt($i, $i))
                        );
                    }
                    $vector->setAt($i,$numberClass::div($vector->getAt($i),$matrix->getAt($i, $i)));
                    $matrix->setAt($i, $i, 1);
                }

                for ($j = ($i - 1) ; $j >= 0; --$j) {
                    $d = $matrix->getAt($i, $i);
                    $c = $matrix->getAt($j, $i);
                    $matrix->setAt($j, $i, 0);
                    for ($k = 0; $k < $i; ++$k) {
                        $matrix->setAt($j, $k,
                            $numberClass::sub(
                                $numberClass::mul($matrix->getAt($j, $k), $d),
                                $numberClass::mul($matrix->getAt($i, $k), $c)
                            )
                        );
                    }
                    $vector->setAt($j, $numberClass::sub(
                        $numberClass::mul($vector->getAt($j), $d),
                        $numberClass::mul($vector->getAt($i), $c)
                    ));
                }
                if (null !== $show) {
                    call_user_func_array($show, array(2 * $N - $i - 2, $matrix, $vector));
                }
            }

            for($i = 0; $i < $size; ++$i) {
                $vector->setAt($i, $numberClass::div($vector->getAt($i),$matrix->getAt($i, $i)));
                $matrix->setAt($i, $i, 1);
            }
            return $vector;
        }

        self::$matrix = $matrix;
        self::$vector = $vector;
        return $haveSolution;
    }

    /**
     * Iterative Gauss-Zeidel algorithm
     * @param CMatrix $matrix
     * @param CVector $vector
     * @param INumber $eps
     * @param INumber $big
     * @param type $show
     * @param type $maxIters
     * @param CVector $start
     * @return CVector vector of result
     */
    public static function gaussZeidel(CMatrix $matrix, CVector $vector, INumber $eps, INumber $big = null, $show = null,  $maxIters = 50, CVector $start = null) {
        self::$errors = array();
        self::$matrix = null;
        self::$vector = null;

        if (self::$clone) {
            $matrix = clone $matrix;
            $vector = clone $vector;
            if (null !== $start) {
                $start = clone $start;
            }
        }

        $N = $matrix->getHeight();
        $M = $matrix->getWidth();
        $L = $vector->getSize();
        if ($N != $L) {
            self::$errors[] = 'Size of matrix do not equal size of vector';
        }

        if ($N != $M) {
            self::$errors[] = 'Matrix is not square';
        }

        if ($matrix->getSubHierarchy() != $vector->getHierarchy()) {
            self::$errors[] = 'Matrix and vector elements do not same';
        }

        $h = $matrix->getSubHierarchy();
        $numberClass = reset(explode(CMatrix::HIERARHY_SEPARATOR, $h));
        $hierarchy = $vector->getSubHierarchy();

        $size = min($N, $M, $L);

        if (null === $start) {
            $vect = array();
            for($i = 0; $i < $size; ++$i) {
                $vect[$i] =  $numberClass::create($hierarchy, 1);
            }
            $start = CVector::create($h, $vect);
        } else {
            $S = $start->getSize();

            if ($S != $size) {
                self::$errors[] = 'Size of matrix do not equal size of start';
            }

            if ($matrix->getSubHierarchy() != $start->getHierarchy()) {
                self::$errors[] = 'Matrix and vector elements do not same';
            }
            $size = min($size, $S);
        }


        $iterCnt = $maxIters;
        $stop = false;

        if (null !== $show) {
            call_user_func_array($show, array('START', $matrix, $vector, $start));
        }

        while((--$iterCnt >= 0) && !$stop) {
            $stop = true;

            for($i = 0; $i < $size; ++$i) {
                $zero = $numberClass::isZero($matrix->getAt($i, $i));
                if ($zero) {
                    for($l = ($i + 1); $l < $size; ++$l) {
                        if (!$numberClass::isZero($matrix->getAt($l, $i))) {
                            $matrix->swap($i, $l);
                            $vector->swap($i, $l);
                            $start->swap($i, $l);
                            $zero = false;
                            break;
                        }
                    }
                }

                if (!$zero) {
                    $prev = $start->getAt($i);
                    $start->setAt($i, $vector->getAt($i));
                    for($j = 0; $j < $size; ++$j) {
                        if ($i == $j) { continue; }
                        $start->setAt($i, $numberClass::sub(
                            $start->getAt($i),
                            $numberClass::mul(
                                $matrix->getAt($i,$j),
                                $start->getAt($j)
                            )
                        ));
                    }
                    $start->setAt($i, $numberClass::div(
                        $start->getAt($i),
                        $matrix->getAt($i,$i)
                    ));

                    $stop = $stop && ($numberClass::cmp(
                        $numberClass::abs(
                            $numberClass::sub($prev,$start->getAt($i))
                        ),
                        $eps
                    ) < 0);

                    if ($numberClass::cmp($big, $numberClass::abs($start->getAt($i))) < 0) {
                        self::$errors[] = 'Elements to big';
                        $stop = true;
                        break;
                    }
                } else {
                    $stop = true;
                }
            }
            if (null !== $show) {
                call_user_func_array($show, array($maxIters - $iterCnt, $matrix, $vector, $start));
            }
        }

        return $start;
    }

    /**
     * Involution to integer power
     * @param INumber $value
     * @param integer $pow
     * @return INumber
     */
    public static function pow(INumber $value, $pow) {
        $pow = (int)abs($pow);
        $numberClass = get_class($value);
        if ($value instanceof IComposite) {
            $one = $numberClass::create($value->getHierarchy(),1);
        } else {
            $one = $numberClass::create(1);
        }

        for ($i = 0; $i < $pow; ++$i) {
            $one = $numberClass::mul($one, $value);
        }
        return $one;
    }

    /**
     * calculates Jacobian
     * @param CMatrix $matrix
     * @param array $pows
     * @return \CMatrix Jacobian
     */
    public static function calculateJacobian(CMatrix $matrix, $pows) {
        $matrix = clone $matrix;
        if (is_array($pows)) {
            $H = $matrix->getHeight();
            $W = $matrix->getWidth();
            $h = $matrix->getSubHierarchy();
            $numberClass = reset(explode(CMatrix::HIERARHY_SEPARATOR, $h));
            $hierarhy = $matrix->getSubHierarchy('', 2);
            for ($i = 0; $i < $H; ++$i) {
                for ($j = 0; $j < $W; ++$j) {
                    if (isset($pows[$i][$j])) {
                        if (empty($hierarhy)) {
                            $multiplier = $numberClass::create($pows[$i][$j]);
                        } else {
                            $multiplier = $numberClass::create($hierarhy, $pows[$i][$j]);
                        }
                        $matrix->setAt($i, $j, $numberClass::mul(
                            $matrix->getAt($i, $j),
                            $multiplier
                        ));
                    }
                }
            }
        }

        return $matrix;
    }

    /**
     * Decrement each pow by 1
     * @param array $pows
     * @return array
     */
    public static function decPows($pows) {
        $decremented = array();
        if (is_array($pows)) {
            $N = count($pows);
            $M = count(reset($pows));
            for ($i = 0; $i < $N; ++$i) {
                $decremented[$i] = array();
                for ($j = 0; $j < $M; ++$j) {
                    if (isset($pows[$i][$j]) && ($pows[$i][$j] > 0)) {
                        $decremented[$i][$j] = $pows[$i][$j] - 1;
                    } else {
                        $decremented[$i][$j] = 0;
                    }
                }
            }
        }
        return $decremented;
    }

    /**
     * Substituted in the equation vector, but do not sum matrix coefficients
     * @param CMatrix $matrix
     * @param CVector $vector
     * @param array $pows
     * @return \CMatrix
     */
    public static function mulMatrixOnVector(CMatrix $matrix, CVector $vector, $pows) {
        $matrix = clone $matrix;
        $H = $matrix->getHeight();
        $W = $matrix->getWidth();
        $h = $matrix->getSubHierarchy();
        $numberClass = reset(explode(CMatrix::HIERARHY_SEPARATOR, $h));
        for ($i = 0; $i < $H; ++$i) {
            for ($j = 0; $j < $W; ++$j) {
                $matrix->setAt($i, $j,
                    $numberClass::mul(
                        $matrix->getAt($i, $j),
                        isset($pows[$i][$j]) ? self::pow($vector->getAt($j), $pows[$i][$j]) : $vector->getAt($j)
                    )
                );
            }
        }
        return $matrix;
    }

    /**
     * Nyuton alghorithm for solving non-linear equations
     * @param CMatrix $matrix
     * @param CVector $vector
     * @param type $pows
     * @param INumber $eps
     * @param INumber $big
     * @param type $show
     * @param type $maxIters
     * @param CVector $start
     * @return CVector result of alghorithm works
     */
    public static function nyuton(CMatrix $matrix, CVector $vector, $pows, INumber $eps, INumber $big, $show = null,  $maxIters = 50, CVector $start = null) {
        self::$errors = array();
        self::$matrix = null;
        self::$vector = null;

        if (self::$clone) {
            $matrix = clone $matrix;
            $vector = clone $vector;
            if (null !== $start) {
                $start = clone $start;
            }
        }

        $N = $matrix->getHeight();
        $M = $matrix->getWidth();
        $L = $vector->getSize();
        if ($N != $L) {
            self::$errors[] = 'Size of matrix do not equal size of vector';
        }

        if ($N != $M) {
            self::$errors[] = 'Matrix is not square';
        }

        if ($matrix->getSubHierarchy() != $vector->getHierarchy()) {
            self::$errors[] = 'Matrix and vector elements do not same';
        }

        $h = $matrix->getSubHierarchy();
        $numberClass = reset(explode(CMatrix::HIERARHY_SEPARATOR, $h));
        $hierarchy = $vector->getSubHierarchy();

        $size = min($N, $M, $L);

        if (null === $start) {
            $vect = array();
            for($i = 0; $i < $size; ++$i) {
                $vect[$i] =  $numberClass::create($hierarchy, 1);
            }
            $start = CVector::create($h, $vect);
        } else {
            $S = $start->getSize();

            if ($S != $size) {
                self::$errors[] = 'Size of matrix do not equal size of start';
            }

            if ($matrix->getSubHierarchy() != $start->getHierarchy()) {
                self::$errors[] = 'Matrix and vector elements do not same';
            }
            $size = min($size, $S);
        }

        $jacob = self::calculateJacobian($matrix, $pows);
        $jacobPows = self::decPows($pows);
        $iterCnt = $maxIters;
        $stop = false;

        if (null !== $show) {
            call_user_func_array($show, array('START', $matrix, $vector, $start));
        }

        for($i = 0; $i < $size; ++$i) {
            $zero = $numberClass::isZero($matrix->getAt($i, $i));
            if ($zero) {
                for($l = ($i + 1); $l < $size; ++$l) {
                    if (!$numberClass::isZero($matrix->getAt($l, $i))) {
                        $matrix->swap($i, $l);
                        $vector->swap($i, $l);
                        $start->swap($i, $l);
                        $jacob->swap($i, $l);
                        $zero = false;
                        break;
                    }
                }
            }

            if ($zero) {
                self::$errors[] = 'There are empty line ' . $i;
                $stop = true;
            }
        }

        if (null !== $show) {
            call_user_func_array($show, array('PREPARED', $matrix, $vector, $start));
        }

        while((--$iterCnt >= 0) && !$stop) {
            $stop = true;

            $f = self::testSolution($matrix, $vector, $start, $pows);
            $temp = self::mulMatrixOnVector($jacob, $start, $jacobPows);
            $tempSol = self::gauss($temp, $f);

            if ($tempSol) {
                for($i = 0; $i < $size; ++$i) {
                    $stop = $stop && ($numberClass::cmp(
                        $numberClass::abs($tempSol->getAt($i)),
                        $eps
                    ) < 0);

                    $start->setAt($i, $numberClass::add(
                        $start->getAt($i),
                        $tempSol->getAt($i)
                    ));

                    if ($numberClass::cmp($big, $numberClass::abs($start->getAt($i))) < 0) {
                        self::$errors[] = 'Elements to big';
                        $stop = true;
                        break;
                    }
                }
            } else {
                $stop = true;
                self::$errors[] = 'There are no solution';
            }

            if (null !== $show) {
                call_user_func_array($show, array($maxIters - $iterCnt, $start, $f, $tempSol));
            }
        }

        return $start;
    }

}
?>

<?php
class CSolver {
    public static $errors = array();
    public static $lineOptimize = true;
    public static $clone = true;
    public static $matrix = null;
    public static $vector = null;
    
    

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
    }
    
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
    
    public static function nyuton2(CMatrix $matrix, CVector $vector, $pows, INumber $eps, INumber $big, $show = null,  $maxIters = 50, $coeficients = null, CVector $start = null) {
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
        
        if ((null === $coeficients) || !is_array($coeficients)) {
            $coeficients = CVector::create($h, array(1));
        } else {
            $coeficients = CVector::create($h, $coeficients);
        }
        $iPows = self::decPows($pows);
        $iterCnt = $maxIters;
        $stop = false;

        /*if (null !== $show) {
            call_user_func_array($show, array('START', $matrix, $vector, $start));
        }*/

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
            
            if ($zero) {
                self::$errors[] = 'There are empty line ' . $i;
                $stop = true;
                break;
            }
        }

        $iterations = CMatrix::create($h, array($start));
        $iterationsSize = $coeficients->getSize();
        
        while((--$iterCnt >= 0) && !$stop) {

            echo '---==[' . ($maxIters - $iterCnt) . "]==---\n";

            $stop = true;

            $iMatrix = self::mulMatrixOnVector($matrix, $start, $iPows);
            $tempSol = self::gauss($iMatrix, $vector);

            echo "---Matr:\n" . $iMatrix . "\n---\n";
            if ($tempSol) {
                $iterations->insertLine(0, $tempSol);
                if ($iterations->getHeight() > $iterationsSize) {
                    $iterations->removeLine($iterationsSize);
                }

                echo "---iterations:\n" . $iterations . "\n---\n";

                for($i = 0; $i < $size; ++$i) {
                    $old = clone $start->getAt($i);

                    $start->setAt($i, 0);
                    for($j = 0; $j < $iterationsSize; ++$j) {
                        $start->setAt($i, $numberClass::add(
                            $start->getAt($i),
                            $numberClass::mul(
                                $iterations->getAt($j, $i),
                                $coeficients->getAt($j)
                            )
                        ));
                    }
                    
                    $stop = $stop && ($numberClass::cmp(
                        $numberClass::abs(
                            $numberClass::sub(
                                $start->getAt($i),
                                $old
                            )
                        ),
                        $eps
                    ) < 0);
                    
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
            
            /*if (null !== $show) {
                call_user_func_array($show, array($maxIters - $iterCnt, $start, $f, $tempSol));
            }*/
        }
        
        return $start;
    }
    
}
?>

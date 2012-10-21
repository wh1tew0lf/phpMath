<?php
require_once '../loader.php';
echo "\n\n\n-------------CVECTOR TEST START------------\n";
$a = CVector::create('CFraction.CInteger',array(CFraction::create('CInteger',1,2),2,3));
$b = CVector::create('CFraction.CInteger',array(-5,2,CFraction::create('CInteger',3,7)));
$zero = CVector::create('CFraction.CInteger',array(0,0,0));
$one = CVector::create('CFraction.CInteger',array(1,1,1));

echo 'A=' . $a . "\n";
echo 'B=' . $b . "\n";
echo '0=' . $zero . "\n";
echo '1=' . $one . "\n";

echo "\n-----Addition-----\n";
echo 'A+B=' . CVector::add($a, $b) . "\n";
echo 'B+A=' . CVector::add($b, $a) . "\n";
echo 'A+0=' . CVector::add($a, $zero) . "\n";
echo '0+A=' . CVector::add($zero, $a) . "\n";

echo "\n-----Substruction-----\n";
echo 'A-B=' . CVector::sub($a, $b) . "\n";
echo 'B-A=' . CVector::sub($b, $a) . "\n";
echo 'A-0=' . CVector::sub($a, $zero) . "\n";
echo '0-A=' . CVector::sub($zero, $a) . "\n";

echo "\n-----Multipliaction-----\n";
echo 'A*B=' . CVector::mul($a, $b) . "\n";
echo 'B*A=' . CVector::mul($b, $a) . "\n";
echo 'A*0=' . CVector::mul($a, $zero) . "\n";
echo '0*A=' . CVector::mul($zero, $a) . "\n";
echo 'A*1=' . CVector::mul($a, $one) . "\n";
echo '1*A=' . CVector::mul($one, $a) . "\n";

echo "\n-----Division-----\n";
echo 'A/B=' . CVector::div($a, $b) . "\n";
echo 'B/A=' . CVector::div($b, $a) . "\n";
echo '0/A=' . CVector::div($zero, $a) . "\n";
echo 'A/1=' . CVector::div($a, $one) . "\n";
echo '1/A=' . CVector::div($one, $a) . "\n";

try {
    echo 'A/0=' . CVector::div($a, $zero) . "\n";
} catch (Exception $e) {
    echo 'error occured: "' . $e->getMessage() . "\"\n";
}

echo "\n-----Neg-----\n";

echo 'neg(A)=' . CVector::neg($a) . "\n";
echo 'neg(B)=' . CVector::neg($b) . "\n";
echo 'neg(0)=' . CVector::neg($zero) . "\n";

echo "\n-----Abs-----\n";

echo 'abs(A)=' . CVector::abs($a) . "\n";
echo 'abs(B)=' . CVector::abs($b) . "\n";
echo 'abs(0)=' . CVector::abs($zero) . "\n";

echo "\n-----GCD-----\n";

echo 'gcd(A,B)=' . CVector::gcd($a, $b) . "\n";
echo 'gcd(A,A)=' . CVector::gcd($a, $a) . "\n";
echo 'abs(A,0)=' . CVector::gcd($a, $zero) . "\n";
echo 'abs(A,1)=' . CVector::gcd($a, $one) . "\n";

echo "\n-----CLONE-----\n";

echo 'clone(A)=' . (clone $a) . "\n";
echo 'clone(0)=' . (clone $zero) . "\n";
echo 'clone(1)=' . (clone $one) . "\n";

echo "\n-----isZero-----\n";

echo 'isZero(0):';
var_dump(CVector::isZero($zero));
echo 'isZero(1):';
var_dump(CVector::isZero($one));
echo 'isZero(B):';
var_dump(CVector::isZero($b));

echo "\n-----isOne-----\n";

echo 'isOne(0):';
var_dump(CVector::isOne($zero));
echo 'isOne(1):';
var_dump(CVector::isOne($one));
echo 'isOne(B):';
var_dump(CVector::isOne($b));

echo "\n-----isNeg-----\n";

echo 'isNeg(0):';
var_dump(CVector::isNeg($zero));
echo 'isNeg(A):';
var_dump(CVector::isNeg($a));
echo 'isNeg(B):';
var_dump(CVector::isNeg($b));

echo "\n-----Compare-----\n";

echo 'cmp(0,1):';
var_dump(CVector::cmp($zero,$one));
echo 'cmp(1,0):';
var_dump(CVector::cmp($one,$zero));
echo 'cmp(0,0):';
var_dump(CVector::cmp($zero,$zero));
echo 'cmp(1,1):';
var_dump(CVector::cmp($one,$one));
echo 'cmp(A,clone A):';
var_dump(CVector::cmp($a,clone $a));
echo 'cmp(B,0):';
var_dump(CVector::cmp($b,$zero));
echo 'cmp(A,B):';
var_dump(CVector::cmp($a,$b));

echo "\n-------------CVECTOR TEST END------------\n\n\n";
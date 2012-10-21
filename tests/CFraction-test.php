<?php
require_once '../loader.php';
echo "\n\n\n-------------CFRACTION TEST START------------\n";
$a = CFraction::create('CInteger',15,10);
$b = CFraction::create('CInteger',-7,3);
$zero = CFraction::create('CInteger',0);
$one = CFraction::create('CInteger',1);

echo 'A=' . $a . "\n";
echo 'B=' . $b . "\n";
echo '0=' . $zero . "\n";
echo '1=' . $one . "\n";

echo "\n-----Addition-----\n";
echo 'A+B=' . CFraction::add($a, $b) . "\n";
echo 'B+A=' . CFraction::add($b, $a) . "\n";
echo 'A+0=' . CFraction::add($a, $zero) . "\n";
echo '0+A=' . CFraction::add($zero, $a) . "\n";

echo "\n-----Substruction-----\n";
echo 'A-B=' . CFraction::sub($a, $b) . "\n";
echo 'B-A=' . CFraction::sub($b, $a) . "\n";
echo 'A-0=' . CFraction::sub($a, $zero) . "\n";
echo '0-A=' . CFraction::sub($zero, $a) . "\n";

echo "\n-----Multipliaction-----\n";
echo 'A*B=' . CFraction::mul($a, $b) . "\n";
echo 'B*A=' . CFraction::mul($b, $a) . "\n";
echo 'A*0=' . CFraction::mul($a, $zero) . "\n";
echo '0*A=' . CFraction::mul($zero, $a) . "\n";
echo 'A*1=' . CFraction::mul($a, $one) . "\n";
echo '1*A=' . CFraction::mul($one, $a) . "\n";

echo "\n-----Division-----\n";
echo 'A/B=' . CFraction::div($a, $b) . "\n";
echo 'B/A=' . CFraction::div($b, $a) . "\n";
echo '0/A=' . CFraction::div($zero, $a) . "\n";
echo 'A/1=' . CFraction::div($a, $one) . "\n";
echo '1/A=' . CFraction::div($one, $a) . "\n";

try {
    echo 'A/0=' . CFraction::div($a, $zero) . "\n";
} catch (Exception $e) {
    echo 'error occured: "' . $e->getMessage() . "\"\n";
}

echo "\n-----Neg-----\n";

echo 'neg(A)=' . CFraction::neg($a) . "\n";
echo 'neg(B)=' . CFraction::neg($b) . "\n";
echo 'neg(0)=' . CFraction::neg($zero) . "\n";

echo "\n-----Abs-----\n";

echo 'abs(A)=' . CFraction::abs($a) . "\n";
echo 'abs(B)=' . CFraction::abs($b) . "\n";
echo 'abs(0)=' . CFraction::abs($zero) . "\n";

echo "\n-----GCD-----\n";

echo 'gcd(A,B)=' . CFraction::gcd($a, $b) . "\n";
echo 'gcd(A,A)=' . CFraction::gcd($a, $a) . "\n";
echo 'abs(A,0)=' . CFraction::gcd($a, $zero) . "\n";
echo 'abs(A,1)=' . CFraction::gcd($a, $one) . "\n";

echo "\n-----CLONE-----\n";

echo 'clone(A)=' . (clone $a) . "\n";
echo 'clone(0)=' . (clone $zero) . "\n";
echo 'clone(1)=' . (clone $one) . "\n";

echo "\n-----isZero-----\n";

echo 'isZero(0):';
var_dump(CFraction::isZero($zero));
echo 'isZero(1):';
var_dump(CFraction::isZero($one));
echo 'isZero(B):';
var_dump(CFraction::isZero($b));

echo "\n-----isOne-----\n";

echo 'isOne(0):';
var_dump(CFraction::isOne($zero));
echo 'isOne(1):';
var_dump(CFraction::isOne($one));
echo 'isOne(B):';
var_dump(CFraction::isOne($b));

echo "\n-----isNeg-----\n";

echo 'isNeg(0):';
var_dump(CFraction::isNeg($zero));
echo 'isNeg(A):';
var_dump(CFraction::isNeg($a));
echo 'isNeg(B):';
var_dump(CFraction::isNeg($b));

echo "\n-----Compare-----\n";

echo 'cmp(0,1):';
var_dump(CFraction::cmp($zero,$one));
echo 'cmp(1,0):';
var_dump(CFraction::cmp($one,$zero));
echo 'cmp(0,0):';
var_dump(CFraction::cmp($zero,$zero));
echo 'cmp(1,1):';
var_dump(CFraction::cmp($one,$one));
echo 'cmp(A,clone A):';
var_dump(CFraction::cmp($a,clone $a));
echo 'cmp(B,0):';
var_dump(CFraction::cmp($b,$zero));
echo 'cmp(A,B):';
var_dump(CFraction::cmp($a,$b));

echo "\n-------------CFRACTION TEST END------------\n\n\n";
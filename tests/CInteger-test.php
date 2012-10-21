<?php
require_once '../loader.php';
echo "\n\n\n-------------CINTEGER TEST START------------\n";
$a = CInteger::create(15);
$b = CInteger::create(-20);
$zero = CInteger::create(0);
$one = CInteger::create(1);

echo 'A=' . $a . "\n";
echo 'B=' . $b . "\n";
echo '0=' . $zero . "\n";
echo '1=' . $one . "\n";

echo "\n-----Addition-----\n";
echo 'A+B=' . CInteger::add($a, $b) . "\n";
echo 'B+A=' . CInteger::add($b, $a) . "\n";
echo 'A+0=' . CInteger::add($a, $zero) . "\n";
echo '0+A=' . CInteger::add($zero, $a) . "\n";

echo "\n-----Substruction-----\n";
echo 'A-B=' . CInteger::sub($a, $b) . "\n";
echo 'B-A=' . CInteger::sub($b, $a) . "\n";
echo 'A-0=' . CInteger::sub($a, $zero) . "\n";
echo '0-A=' . CInteger::sub($zero, $a) . "\n";

echo "\n-----Multipliaction-----\n";
echo 'A*B=' . CInteger::mul($a, $b) . "\n";
echo 'B*A=' . CInteger::mul($b, $a) . "\n";
echo 'A*0=' . CInteger::mul($a, $zero) . "\n";
echo '0*A=' . CInteger::mul($zero, $a) . "\n";
echo 'A*1=' . CInteger::mul($a, $one) . "\n";
echo '1*A=' . CInteger::mul($one, $a) . "\n";

echo "\n-----Division-----\n";
echo 'A/B=' . CInteger::div($a, $b) . "\n";
echo 'B/A=' . CInteger::div($b, $a) . "\n";
echo '0/A=' . CInteger::div($zero, $a) . "\n";
echo 'A/1=' . CInteger::div($a, $one) . "\n";
echo '1/A=' . CInteger::div($one, $a) . "\n";

try {
    echo 'A/0=' . CInteger::div($a, $zero) . "\n";
} catch (Exception $e) {
    echo 'error occured: "' . $e->getMessage() . "\"\n";
}

echo "\n-----Neg-----\n";

echo 'neg(A)=' . CInteger::neg($a) . "\n";
echo 'neg(B)=' . CInteger::neg($b) . "\n";
echo 'neg(0)=' . CInteger::neg($zero) . "\n";

echo "\n-----Abs-----\n";

echo 'abs(A)=' . CInteger::abs($a) . "\n";
echo 'abs(B)=' . CInteger::abs($b) . "\n";
echo 'abs(0)=' . CInteger::abs($zero) . "\n";

echo "\n-----GCD-----\n";

echo 'gcd(A,B)=' . CInteger::gcd($a, $b) . "\n";
echo 'gcd(A,A)=' . CInteger::gcd($a, $a) . "\n";
echo 'abs(A,0)=' . CInteger::gcd($a, $zero) . "\n";
echo 'abs(A,1)=' . CInteger::gcd($a, $one) . "\n";

echo "\n-----CLONE-----\n";

echo 'clone(A)=' . (clone $a) . "\n";
echo 'clone(0)=' . (clone $zero) . "\n";
echo 'clone(1)=' . (clone $one) . "\n";

echo "\n-----isZero-----\n";

echo 'isZero(0):';
var_dump(CInteger::isZero($zero));
echo 'isZero(1):';
var_dump(CInteger::isZero($one));
echo 'isZero(B):';
var_dump(CInteger::isZero($b));

echo "\n-----isOne-----\n";

echo 'isOne(0):';
var_dump(CInteger::isOne($zero));
echo 'isOne(1):';
var_dump(CInteger::isOne($one));
echo 'isOne(B):';
var_dump(CInteger::isOne($b));

echo "\n-----isNeg-----\n";

echo 'isNeg(0):';
var_dump(CInteger::isNeg($zero));
echo 'isNeg(A):';
var_dump(CInteger::isNeg($a));
echo 'isNeg(B):';
var_dump(CInteger::isNeg($b));

echo "\n-----Compare-----\n";

echo 'cmp(0,1):';
var_dump(CInteger::cmp($zero,$one));
echo 'cmp(1,0):';
var_dump(CInteger::cmp($one,$zero));
echo 'cmp(0,0):';
var_dump(CInteger::cmp($zero,$zero));
echo 'cmp(1,1):';
var_dump(CInteger::cmp($one,$one));
echo 'cmp(A,clone A):';
var_dump(CInteger::cmp($a,clone $a));
echo 'cmp(B,0):';
var_dump(CInteger::cmp($b,$zero));
echo 'cmp(A,B):';
var_dump(CInteger::cmp($a,$b));

echo "\n-------------CINTEGER TEST END------------\n\n\n";
?>

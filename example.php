<?php
require_once 'loader.php';

echo "-------------PROG START------------\n";

//fact of n
$n = 200;
$fact = CBigInteger::create(1);
echo 'Factorial of [' . $n . '] = ';
for(;$n > 0; --$n) {
    $fact = CBigInteger::mul($fact, CBigInteger::create($n));
}
echo $fact . "\n\n";

//Gauss
$matrix = CMatrix::create('CFraction.CInteger', array(
    array(1,2,5),
    array(3,3,5),
    array(1,5,3),
));
$vector = CVector::create('CFraction.CInteger', array(1,1,1));

$solution = CSolver::gauss($matrix, $vector);
if ($solution !== false) {
    echo 'Solution is: ' . $solution . "\n";
    
    echo CSolver::testSolution($matrix, $vector, $solution);
} else {
    echo "There are no solution\n";
}

echo "\n-------------PROG END------------\n";
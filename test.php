<?php
require_once 'loader.php';

echo "\n\n\n-------------PROG START------------\n";

$N = rand(3,5);

$test = array();
for($i = 0; $i < $N; ++$i) {
    $test[$i] = array();
    $sum = 0;
    for($j = 0; $j < $N; ++$j) {
        $test[$i][$j] = rand(1,15);
        $sum = $sum + $test[$i][$j] * $test[$i][$j];
    }
    $test[$i][$i] = (int)ceil(sqrt($sum + 1));
}


$pows = array();
for($i = 0; $i < $N; ++$i) {
    $pows[$i] = array();
    for($j = 0; $j < $N; ++$j) {
        $pows[$i][$j] = rand(1,4);
    }
}

$vt = array();
for($i = 0; $i < $N; ++$i) {
    $vt[$i] = rand(1, 20);
}

$st = array();
for($i = 0; $i < $N; ++$i) {
    $st[$i] = CFraction::create('CInteger',rand(0,20),rand(1,20));
}

$coef = array(
    CFraction::create('CInteger',1,2),
    CFraction::create('CInteger',1,4),
    CFraction::create('CInteger',1,8),
    CFraction::create('CInteger',1,16),
    CFraction::create('CInteger',1,32),
    CFraction::create('CInteger',1,64),
    CFraction::create('CInteger',1,128),
    CFraction::create('CInteger',1,256),
    CFraction::create('CInteger',1,512),
    CFraction::create('CInteger',1,1024),
    CFraction::create('CInteger',1,2048),
    CFraction::create('CInteger',1,2048),
    /*/
    CFraction::create('CInteger',1,2),
    CFraction::create('CInteger',1,3),
    CFraction::create('CInteger',1,6),
    //*/
);

$matrix = CMatrix::create('CFraction.CInteger',$test);
$vector = CVector::create('CFraction.CInteger',$vt);
$start = CVector::create('CFraction.CInteger',$st);
//$vector = CSolver::testSolution($matrix, CVector::create('CFraction.CInteger', array(0)), $start, $pows);
CFraction::setView(true);
CFraction::setPrecision(6);
$solution = CSolver::nyuton2(
    $matrix,
    $vector,
    $pows,
    CFraction::create('CInteger', 1, 100000),
    CFraction::create('CInteger', 10000, 1),
    array('CSolver','showNyuton'),
    256,
    $coef,
    $start
);

if (empty(CSolver::$errors)) {
    echo 'Solution: ' . $solution . "\n";
    echo 'Tested solution: ' . CSolver::testSolution($matrix, $vector, $solution, $pows) . "\n";
    echo 'Iter: ' . CSolver::$iter . "\n";
} else {
    echo "No solution\n";
    var_dump(CSolver::$errors);
}
//*/


echo "\n-------------PROG END------------\n\n\n";
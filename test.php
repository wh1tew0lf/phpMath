<?php
require_once 'loader.php';

echo "\n\n\n-------------PROG START------------\n";

$test = array(
    array(4, 1, 2, 1),
    array(2, 5, 1, 1),
    array(1, 8, 16, 1),
    array(1, 3, 1, 5),
);

$pows = array(
    array(2, 1, 2, 1),
    array(3, 2, 1, 2),
    array(1, 2, 2, 1),
    array(2, 1, 3, 2),
);

$vt = array(3, 2, 6, 4);
$st = array(
    CFraction::create('CInteger',1,1),
    CFraction::create('CInteger',1,1),
    CFraction::create('CInteger',1,1),
    CFraction::create('CInteger',1,1),
);


$coef = array(
    /*
    CFraction::create('CInteger',1,2),
    CFraction::create('CInteger',1,4),
    CFraction::create('CInteger',1,8),
    CFraction::create('CInteger',1,16),
    CFraction::create('CInteger',1,32),
    CFraction::create('CInteger',1,64),
    CFraction::create('CInteger',1,128),
    CFraction::create('CInteger',1,256),
    CFraction::create('CInteger',1,8),
    //*/
    CFraction::create('CInteger',1,2),
    CFraction::create('CInteger',1,3),
    CFraction::create('CInteger',1,6),
);

$matrix = CMatrix::create('CFraction.CInteger',$test);
$vector = CVector::create('CFraction.CInteger',$vt);
$start = CVector::create('CFraction.CInteger',$st);
CFraction::setView(true);
CFraction::setPrecision(6);
$solution = CSolver::nyuton2(
    $matrix,
    $vector,
    $pows,
    CFraction::create('CInteger', 1, 10000),
    CFraction::create('CInteger', 10000, 1),
    array('CSolver','showNyuton'),
    100,
    $coef,
    $start
);

if (empty(CSolver::$errors)) {
    echo 'Solution: ' . $solution . "\n";
    echo 'Tested solution: ' . CSolver::testSolution($matrix, $vector, $solution, $pows) . "\n";
} else {
    echo "No solution\n";
    var_dump(CSolver::$errors);
}
//*/


echo "\n-------------PROG END------------\n\n\n";
<?php
if (!isset($argv) || (count($argv) < 3)) {
    die("Error\n");
}

require_once 'loader.php';

$N = $argv[1]; // размер матрицы
$powMin = 1;
$powMax = 2;
$elMin = 1;
$elMax = 100;
$maxIters = $argv[2];   // максимальное количество итераций
$coefs = explode('-', $argv[3]);

$test = array();
for($i = 0; $i < $N; ++$i) {
    $test[$i] = array();
    $sum = 0;
    for($j = 0; $j < $N; ++$j) {
        $test[$i][$j] = rand($elMin, $elMax);
        $sum = $sum + $test[$i][$j] * $test[$i][$j];
    }
    $test[$i][$i] = (int)ceil(sqrt($sum + 1));
}

for($i = 0; $i < $N; ++$i) {
    for($j = 0; $j < $N; ++$j) {
        if ($i != $j) {
            $test[$i][$j] = CFraction::create('CInteger', $test[$i][$j], $test[$i][$i]);
        }
    }
    $test[$i][$i] = CFraction::create('CInteger', 1, 1);
}


$pows = array();
for($i = 0; $i < $N; ++$i) {
    $pows[$i] = array();
    for($j = 0; $j < $N; ++$j) {
        $pows[$i][$j] = rand($powMin, $powMax);
    }
}

$vt = array();
for($i = 0; $i < $N; ++$i) {
    $vt[$i] = rand($elMin, $elMax);
}

$st = array();
for($i = 0; $i < $N; ++$i) {
    $st[$i] = CFraction::create('CInteger',rand(0,20),rand(1,20));
}

$coef = CVector::create('CFraction.CInteger', $coefs);
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
    null, //array('CSolver','showNyuton'),
    $maxIters,
    $coef,
    $start
);

if (empty(CSolver::$errors)) {
    echo CSolver::$iter . "\n";
    echo $solution . "\n";
    echo CSolver::testSolution($matrix, $vector, $solution, $pows) . "\n";
} else {
    echo "error\n";
    foreach(CSolver::$errors as $error) {
        echo $error . "\n";
    }
}


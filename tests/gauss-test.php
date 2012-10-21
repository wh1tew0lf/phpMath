<?php
require_once '../loader.php';

$N = 10;
$min = 1;
$max = 150;
CFraction::setView(true);
CFraction::setPrecision(1);

echo "\n\n\n-------------GAUSS TEST START------------\n";

$m_data = array();
$v_data = array();
for($i = 0; $i < $N; ++$i) {
    $m_data[$i] = array();
    for($j = 0; $j < $N; ++$j) {
        $m_data[$i][$j] = CFraction::create('CInteger', rand($min,$max), rand($min,$max));
    }
    $v_data[$i] = CFraction::create('CInteger', rand($min,$max), rand($min,$max));
}

$matrix = CMatrix::create('CFraction.CInteger', $m_data);
$vector = CVector::create('CFraction.CInteger', $v_data);

$solution = CSolver::gauss(
    $matrix,
    $vector,
    array('CSolver', 'showGaussIter')
);

echo CSolver::testSolution(
    $matrix,
    $vector,
    $solution
) . "\n";


echo "\n\n\n-------------GAUSS TEST END------------\n";
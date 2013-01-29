<?php
$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$interfaces = $path . 'interfases' . DIRECTORY_SEPARATOR;
$classes = $path . 'classes' . DIRECTORY_SEPARATOR;
$algorithms = $path . 'algorithms' . DIRECTORY_SEPARATOR;

require_once $interfaces . 'INumber.php';
require_once $interfaces . 'ISingle.php';
require_once $interfaces . 'IComposite.php';

require_once $classes . 'CInteger.php';
require_once $classes . 'CBigInteger.php';
require_once $classes . 'CFraction.php';
require_once $classes . 'CVector.php';
require_once $classes . 'CMatrix.php';

require_once $algorithms . 'CSolver.php';
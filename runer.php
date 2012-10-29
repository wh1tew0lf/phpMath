<?php
/* This file should run many tests and collect data that its return*/

$TESTS = 500;

$params = array(
    'N' => array(3),
    //'diag' => array(0,1),
    'maxIters' => array(150),
    'coefs' => array(
        array(1),
        
        array('5/6', '1/6'),
        array('2/3', '1/3'),
        array('1/2', '1/2'),
        array('1/3', '2/3'),
        array('1/6', '5/6'),

        /*array('1/2', '1/3', '1/6'),
        array('1/3', '1/2', '1/6'),
        array('1/6', '1/2', '1/3'),
        array('1/6', '1/3', '1/2'),

        array('1/2', '1/4', '1/4'),
        array('1/4', '1/2', '1/4'),
        array('1/4', '1/4', '1/2'),

        array('1/6', '1/6', '2/3'),
        array('1/6', '2/3', '1/6'),
        array('2/3', '1/6', '1/6'),*/

        /*array('1/64', '1/64', '1/32', '1/16', '1/8', '1/4', '1/2'),
        array('1/64', '1/64', '1/32', '1/16', '1/8', '1/4', '1/2'),
        array('1/64', '1/64', '1/32', '1/16', '1/8', '1/4', '1/2'),
        array('1/2', '1/4', '1/8', '1/16', '1/32', '1/4', '1/2'),*/
    )

);

function start($params) {
    $start = array();
    foreach($params as $key => $param) {
        $start[$key] = $param[0];
    }
    return $start;
}

function isEnd($params, $curr) {
    $end = true;
    foreach($params as $key => $param) {
        $max = count($param) - 1;
        $finded = array_search($curr[$key], $params[$key]);
        $end = $end && ($finded == $max);
    }
    return $end;
}

function nextP($params, $curr) {
    $keys = array_reverse(array_keys($params));
    foreach($keys as $key) {
        $finded = array_search($curr[$key], $params[$key]);
        $max = count($params[$key]) - 1;
        if ($finded >= $max) {
            $curr[$key] = $params[$key][0];
        } else {
            $curr[$key] = $params[$key][++$finded];
            break;
        }
    }
    return $curr;
}


function prepareParams($params) {
    $params['coefs'] = implode('-', $params['coefs']);
    return implode(' ', $params);
}

$res = array();
$curr = false;
$key = 0;
while (!$curr || !isEnd($params, $curr)) {

    if (false === $curr) {
        $curr = start($params);
    } else {
        $curr = nextP($params, $curr);
    }

    $res[$key] = array(
        'params' => prepareParams($curr),
        'solved' => 0,
        'errored' => 0,
        'errors' => array(),
        'unsolved' => 0,
    );

    echo "---==[" . $res[$key]['params'] . "]==---\n";

    for($i = 0; $i < $TESTS; ++$i) {
        $output = array();
        $ret = 0;

        exec('php tst.php ' . $res[$key]['params'], $output, $ret);
        if (($ret != 0) || (count($output) < 1)) {
            ++$res[$key]['errored'];
            if (isset($res[$key]['errors']['undefined'])) {
                ++$res[$key]['errors']['undefined'];
            } else {
                $res[$key]['errors']['undefined'] = 1;
            }
        } elseif('error' == $output[0]) {
            ++$res[$key]['errored'];

            $errors = implode('; ', $output);
            if (preg_match('/\[[0-9.+-]+\]/i', $errors)) {
                $errors = preg_replace('/\[[0-9.+-]+\]/i', '', $errors);
            }
            if (isset($res[$key]['errors'][$errors])) {
                ++$res[$key]['errors'][$errors];
            } else {
                $res[$key]['errors'][$errors] = 1;
            }
        } elseif($curr['maxIters'] == $output[0]) {
            ++$res[$key]['unsolved'];
        } else {
            ++$res[$key]['solved'];
        }
    }
    ++$key;
}

file_put_contents('./finish.txt', serialize($res));

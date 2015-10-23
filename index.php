<?php

//include_once './admin.php';

include_once './index.php';
include_once './connect.php';
include_once './classes/expressions.php';
    
include_once './classes/parser.class.php';
include_once './classes/index.class.php';

$parser = new parser();
$index = new index();

$temp_index = array();
$tokens = array("admits","and","also");
$tokens_rpn = $parser->infix_to_rpn($tokens);

foreach ($tokens_rpn as $token) {
    if (!in_array($token, $parser::operators_dictionary)) {
        $temp_index[$token] = $index->get_documents($token);
    }
    else {
        $temp_index[$token] = false;
    }
}

$root = $parser->create_tree(new ArrayIterator($temp_index));

$result = $root->evaluate();

print_r($result);




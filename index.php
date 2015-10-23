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
$tokens = array("(","a","and","b",")","and","(","not","c",")");
//$tokens = array("admits","and","also");
$tokens_rpn = $parser->infix_to_rpn($tokens);

foreach ($tokens_rpn as $token) {
    if (!in_array($token, $parser::operators_dictionary)) {
        $temp_index[] = array("term" => $token,"posting" => $index->get_documents($token));
    }
    else {
        $temp_index[] = array("term" => $token,"posting" => null );
    }
}
print_r($temp_index);
$root = $parser->create_tree(new ArrayIterator($temp_index));

print_r($root);

//$result = $root->evaluate();

//print_r($result);




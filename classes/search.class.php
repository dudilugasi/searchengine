<?php

include_once './classes/expressions.php';
include_once './classes/parser.class.php';

class search {

    public $index = null;
    public $storage = null;

    function __construct(index $index, storage $storage) {
        $this->index = $index;
        $this->storage = $storage;
    }

    function search_documents($search_query) {
        $docs = array();
        $temp_index = array();
        $tokens = explode(" ", $search_query);

        foreach ($tokens as $token) {
            strtolower($token);
        }

        $tokens_rpn = $this->parser->infix_to_rpn($tokens);

        foreach ($tokens_rpn as $token) {
            if (!in_array($token, $parser::operators_dictionary)) {
                $temp_index[] = array("term" => $token, "posting" => $this->index->get_documents($token));
            } else {
                $temp_index[] = array("term" => $token, "posting" => null);
            }
        }

        if (empty($temp_index)) {
            return array();
        } 
        
        $root = $this->parser->create_tree(new ArrayIterator($temp_index));

        if (!is_null($root)) {
            return array();
        }
        
        $result = $root->evaluate();

        foreach ($result as $docid) {
            $docs[] = $this->storage->get_document($docid);
        }
        
        return $docs;
    }

}

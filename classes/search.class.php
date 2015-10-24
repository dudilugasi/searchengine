<?php

include_once './classes/expressions.php';
include_once './classes/parser.class.php';
include_once './lib/stopwords.php';

class search {

    public $index = null;
    public $storage = null;
    public $parser = null;

    function __construct(index $index, storage $storage) {
        $this->index = $index;
        $this->storage = $storage;
        $this->parser = new parser();
    }

    function search_documents($search_query) {
        global $stopwords;
        $docs = array();
        $temp_index = array();
        $tokens = explode(" ", $search_query);

        foreach ($tokens as $key => $value) {

            $token = strtolower($value);

            if (mb_substr($token, 1, 1) != '"' && mb_substr($token, -1) != '"') {
                if (in_array($token, $stopwords)) {
                    unset($tokens[$key]);
                    continue;
                }
            } else {
                $token = substr($token, 1, -1);
            }

            $tokens[$key] = $token;
        }

        $tokens_rpn = $this->parser->infix_to_rpn($tokens);

        foreach ($tokens_rpn as $token) {
            if (!in_array($token, parser::operators_dictionary)) {
                $temp_index[] = array("term" => $token, "posting" => $this->index->get_documents($token));
            } else {
                $temp_index[] = array("term" => $token, "posting" => null);
            }
        }

        if (empty($temp_index)) {
            return array();
        }

        $root = $this->parser->create_tree(new ArrayIterator($temp_index));

        if (is_null($root)) {
            return array();
        }

        $result = $root->evaluate();


        foreach ($result as $docid => $offset) {
            $docs[$docid] = $this->storage->get_document_meta($docid);
            foreach ($tokens as $token) {
                $docs[$docid]["excerpt"] = $this->highlight($docs[$docid]["excerpt"], $token);
            }
        }

        return $docs;
    }

    function highlight($text, $words) {
        preg_match_all('~\w+~', $words, $m);
        if (!$m)
            return $text;
        $re = '~\\b(' . implode('|', $m[0]) . ')\\b~i';
        return preg_replace($re, '<b>$0</b>', $text);
    }

}

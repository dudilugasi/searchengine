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
        global $all_docs;
        $all_docs = $this->index->get_documents_ids();
        $docs = array();
        $temp_index = array();
        $tokens = explode(" ", $search_query);
        
        $temp_tokens = array();
        foreach ($tokens as $token) {
            if (substr($token,-1) == "*") {
                $jokers = $this->get_joker($token);
                foreach ($jokers as $value) {
                    $temp_tokens[] = $value;
                }
            }
            else {
                $temp_tokens[] = $token;
            }
        }

        $tokens = $temp_tokens;
        foreach ($tokens as $key => $value) {

            $token = strtolower($value);

            //if not in qutation marks then check if in stoplist
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

        $tokens = array_diff($tokens, parser::operators_dictionary);


        foreach ($result as $docid => $offset) {
            $docs[$docid] = $this->storage->get_document_meta($docid, $offset);
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

    function get_joker($term) {
        global $conn;
        $terms = array();
        
        $term = substr($term,0, -1);
        $sql = "SELECT term FROM `se_index` WHERE `term` LIKE '$term%'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                 $terms[] = $row["term"]; 
            }
        }
        $terms[] = $term;
        return explode(" ",implode(" or ", $terms));
    }

}

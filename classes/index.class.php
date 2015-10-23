<?php

class index {

    function save_documents($term, $hits, $docs) {
        global $conn;

        //check if the values are correct
        if ($term === null || $docs === null || $hits === null || trim($term) == '') {
            return false;
        }

        if (!is_string($term) || !is_array($docs) || !is_integer($hits)) {
            return false;
        }
  
        $posting = $this->get_documents($term);
        $hits += count($posting);
        
        foreach ($docs as $doc => $value) {
            $posting[$doc] = $value;
        }

        $sql = "INSERT"
                . " into `se_index`(`term`,`hits`, `posting`) "
                . "VALUES ('" . $term . "'," . $hits . ",'" . $conn->real_escape_string(serialize($posting)) . "')"
                . " ON DUPLICATE KEY UPDATE hits=VALUES(hits) , posting=VALUES(posting)";

        
        return ($conn->query($sql) === TRUE) ? true : false;
    }

    function get_documents($term) {
        global $conn;
        
        if ($term === null || trim($term) == '') {
            return array();
        }

        $sql = "SELECT `posting` FROM `se_index` WHERE `term` = '" . $conn->real_escape_string($term) . "'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $posting = unserialize($row["posting"]);
            return $posting;
        }
        
        return array();
        
    }
    
    function get_index() {
        
    }

}

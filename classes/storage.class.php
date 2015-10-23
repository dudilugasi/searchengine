<?php

define("STORAGE_DIR", "storage/");

class storage {

    function save_document($document) {
        global $conn;
        if (!is_array($document) || count($document) != 4) {
            return false;
        }
        $docid = $this->get_doc_id();
        $fp = fopen(STORAGE_DIR . $docid . '.txt', 'a');
        fwrite($fp, serialize($document));
        fclose($fp);

        $sql = "INSERT INTO "
                . "`se_documents` "
                . "(`docid`, `title`, `author`, `year`) "
                . "VALUES"
                . " ('" .
                $conn->real_escape_string($docid) . "', '"
                . $conn->real_escape_string($document["title"]) . "', '"
                . $conn->real_escape_string($document["author"]) . "', '"
                . $conn->real_escape_string($document["year"]) . "');";
        $conn->query($sql);
        return $docid;
    }

    function get_document($docid) {
        if (!is_integer($docid) || $docid < 0) {
            return null;
        }
        $file_name = STORAGE_DIR . $docid . '.txt';
        if (!file_exists($file_name)) {
            return null;
        }
        $content = file_get_contents($file_name);
        return unserialize($content);
    }

    function get_doc_id() {
        $file_name = STORAGE_DIR . 'count.txt';
        $counter = 0;
        if (file_exists($file_name)) {
            $file = fopen($file_name, 'r');
            $counter = (int) fgets($file);
        }
        $file = fopen($file_name, 'w');
        fputs($file, $counter + 1);
        fclose($file);
        return $counter;
    }

    function delete_storage() {
        $fp = opendir(STORAGE_DIR);
        while (false !== ($file = readdir($fp))) {
            if (is_file(STORAGE_DIR . $file)) {
                unlink(STORAGE_DIR . $file);
            }
        }
    }

    function remove_docs($docs) {
        global $conn;
        $sql = "UPDATE `se_documents` SET `exist`= 0 WHERE `docid` IN ( " . implode(",", $docs) . " )";

        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

}

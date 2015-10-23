<?php

include_once 'connect.php';
include_once 'classes/storage.class.php';
include_once 'classes/index.class.php';
include_once 'classes/search.class.php';

if (isset($_GET["search"])) {
    $search = new search($index, $storage);
    $results = $search->search_documents($_GET["search"]);
}

//demo
$results = array();
$results[] = array(
    "id"=>2,
    "title"=>"the nest book ever",
    "author"=>"steve mcsteven",
    "year"=>1989,
    "extract"=>"An associative array. This function treats keys as variable names and values as variable values. For each key/value pair it will create a variable in the current symbol table, subject to flags and prefix parameters.");

?>

<!DOCTYPE html>
<html>
    <head>
        <title>search</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="search_bar">
            <form action="" method="">
                <input type="text" name="search" />
                <input type="submit">
            </form>
        </div>
        <div class="results-container">
            <?php if ($results): ?>
            <?php foreach ($results as $result): ?>
            <section class="result">
                <h1><a href="single-document.php?doc=<?php echo $result["id"] ?>">
                    <?php echo $result["title"] ." ( year: " . $result["year"] . " )" ?>
                </a></h1>
                <h2>Author: <?php echo $result["author"] ?></h2>
                <p><?php echo $result["extract"] ?></p>
            </section>
            <?php  endforeach; ?>
            <?php endif; ?>
            
        </div>
    </body>
</html>
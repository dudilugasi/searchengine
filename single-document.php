<?php

include_once 'connect.php';
include_once 'classes/storage.class.php';

if (isset($_GET["docid"])) {
    $storage = new $storage();
    
    $docid = $_GET["docid"];
    
    $document = $storage->get_document($docid);
    
    
}

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
            <form action="search.php" method="get">
                <input type="text" name="search" />
                <input type="submit">
            </form>
        </div>
        <section>
            <?php if ($document): ?>
            <h1><?php echo $document[1] ." ( year: " . $document[4] . " )" ?></h1>
            <h2>Author: <?php echo $document[2] ?></h2>
            <?php echo $document[0] ?>
            <?php endif; ?>
        </section>
    </body>
</html>
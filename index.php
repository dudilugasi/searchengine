<?php
include_once 'connect.php';
include_once 'classes/storage.class.php';
include_once 'classes/index.class.php';
include_once 'classes/search.class.php';

$results = array();
$message = "";
if (isset($_GET["search"])) {
    $message = "no results";
    $index = new index();
    $storage = new storage();
    $search = new search($index, $storage);
    $results = $search->search_documents($_GET["search"]);
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
            <form action="" method="">
                <input type="text" name="search" />
                <input type="submit">
            </form>
        </div>
        <div class="results-container">
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $result): ?>
                    <?php if ($result["exist"] == 1): ?>
                        <section class="result">
                            <h1><a href="single-document.php?doc=<?php echo $result["docid"] ?>">
                                    <?php echo $result["chapter_name"] . " ( chapter number: " . $result["chapter_num"] . " )" ?>
                                </a></h1>
                        </section>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
            <p>
                <?php echo $message; ?>
            </p>
            <?php endif; ?>

        </div>
    </body>
</html>
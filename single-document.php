<?php
include_once 'connect.php';
include_once 'classes/storage.class.php';


$document = false;
if (isset($_GET["doc"])) {
    $storage = new storage();

    $docid = is_numeric($_GET["doc"]) ? intval($_GET["doc"]) : null;

    if (!is_null($docid)) {
        $document = $storage->get_document($docid);
    }

    //print_r($document);
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
        <section>
            <?php if ($document): ?>
                <h1><?php echo $document["title"] . " ( chapter number: " . $document["chapter"] . " )" ?></h1>
                <div class="contet">
                    <?php echo nl2br($document["content"]) ?>
                </div>
            <?php endif; ?>
        </section>
    </body>
</html>
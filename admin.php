<?php
include_once 'classes/storage.class.php';
include_once 'classes/index.class.php';
include_once 'classes/indexer.class.php';
include_once 'connect.php';



if (isset($_POST["remove"]) && ($_POST["remove"] == "remove")) {
    if (isset($_POST["docs"])) {
        $storage = new storage();
        $index = new index();
        $indexer = new indexer($index, $storage);
        $storage->remove_docs($_POST["docs"]);
    }
}

function get_documents() {
    global $conn;
    $sql = "SELECT * FROM `se_documents` WHERE `exist` = 1";

    return $conn->query($sql);
}
?>



<!DOCTYPE html>
<html>
    <head>
        <title>admin</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>
            <h1>add new sources</h1>
            <button><a href="get_sources.php">add now!</a></button>
        </div>

        <div>
            <?php $results = get_documents(); ?>
            <h1>remove results</h1>
                    <?php if ($results->num_rows > 0): ?>
            <form action="" method="post">
                <table>
                    <thead>
                        <tr>
                            <th>book</th>
                            <th>author</th>
                            <th>year</th>
                            <th>remove</th>
                        </tr>
                    </thead>

                        <tbody>
                            <?php while ($row = $results->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row["title"] ?></td>
                                    <td><?php echo $row["author"] ?></td>
                                    <td><?php echo $row["year"] ?></td>
                                    <td><input type="checkbox" name="docs[]" value="<?php echo $row["docid"] ?>"/></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                </table>
                    <input type="submit" value="remove this items" />
                    <input type="hidden" value="remove" name="remove" />
                </form>
            <?php else: ?>
            <p>no documents</p>
                    <?php endif; ?>
        </div>
    </body>
</html>

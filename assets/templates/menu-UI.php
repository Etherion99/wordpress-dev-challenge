<?php
$links = get_link_results();

$table = new Link_Status_Table();
$table->set_links($links);
$table->prepare_items();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Etherion Kit Tools</title>
</head>
<body>
    <div class="wrap">
        <h1>Links Status</h1>
        <?php $table->display(); ?>
    </div>
</body>
</html>
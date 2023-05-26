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
    <h1>Links Status</h1>
    <?php $table->display(); ?>

    <h1>Posts API Settings</h1>

    <?php $auth_key = get_option('auth_key'); ?>

    <form method="post" action="options.php">
	    <?php settings_fields('etherion-tools-settings-group'); ?>
	    <?php do_settings_sections('etherion-tools-settings-group'); ?>
        <label for="auth_key">Auth key:</label>
        <input type="text" id="auth_key" name="auth_key" value="<?php echo esc_attr($auth_key); ?>" />
	    <?php submit_button('Save'); ?>
    </form>
</body>
</html>
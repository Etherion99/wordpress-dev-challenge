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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo plugins_url( '../css/etherion-tools.css', __FILE__ ) . '?v=' . time(); ?>">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</head>
<body>
    <h1 class="text-center mt-4"><?php echo __('Links Status', 'etherion-tools') ?></h1>

    <div class="row justify-content-center">
        <div class="col-11">
	        <?php $table->display(); ?>
        </div>
    </div>

    <h1 class="text-center"><?php echo __('Posts API Settings', 'etherion-tools') ?></h1>

    <?php $auth_key = get_option('auth_key'); ?>

    <div class="row justify-content-center">
        <div class="col-11">
            <form method="post" action="options.php" class="container">
		        <?php settings_fields('etherion-tools-settings-group'); ?>
		        <?php do_settings_sections('etherion-tools-settings-group'); ?>

                <div class="row my-5">
                    <label for="auth_key" class="col-2 col-form-label"><?php echo __('Auth key', 'etherion-tools').':' ?></label>
                    <div class="col-10">
                        <input type="text" id="auth_key" name="auth_key" value="<?php echo esc_attr($auth_key); ?>" class="form-control" />
                    </div>
                </div>

		        <div class="row justify-content-center">
			        <?php submit_button(esc_attr__('Save', 'etherion-tools')); ?>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
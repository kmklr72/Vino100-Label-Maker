<?php
/**
 * Plugin Name: Vino100 Label Maker
 * Description: Label maker for Vino 100 style labels.
 * Author: Kevin Murek (kmklr72@gmail.com)
 * Version: 1.0
 */

/**
 * Directory constant. Used in file paths.
 *
 * @package Vino100 Label Maker
 */
define('TB_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * URL constant. Used in URL paths.
 *
 * @package Vino100 Label Maker
 */
define('TB_PLUGIN_URL', plugin_dir_url(__FILE__));

class TB_Label_Maker
{
    /**
     * Plugin constructor. Runs all of the main methods to start the plugin.
     *
     * @access public
     *
     * @return void
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    public function admin_menu()
    {
        add_management_page('Vino Label Maker', 'Vino Label Maker', 'publish_pages', 'vino-label-maker', array($this, 'label_maker_form'));
    }

    public function label_maker_form()
    {
        $config = get_option('tb_label_maker_config');

        if (isset($_POST['createlabel']))
        {
            if (!$this->in_multiarray($_POST['region']))
            {
                $config['regions'][] = array(
                    'slug'  => strtolower(str_replace(' ', '_', $_POST['region'])),
                    'name'  => $_POST['region'],
                );
            }

            if (!$this->in_multiarray($_POST['type']))
            {
                $config['regions'][] = array(
                    'slug'  => strtolower(str_replace(' ', '_', $_POST['type'])),
                    'name'  => $_POST['type'],
                );
            }
        }
        else
        {

?>
<div class="wrap">
    <h2>Vino100 Label Maker</h2>
</div>

<form action="" method="post" name="createlabel" id="createlabel" class="validate">

<?php wp_nonce_field('vino100-label-maker'); ?>

<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="product_name">Product Name <span class="description">(required)</span></label></th>
		<td><input name="product_name" type="text" id="product_name" class="regular-text" value="" aria-required="true" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="product_desc">Description </label></th>
		<td><textarea name="product_desc" id="product_desc" rows="5" cols="30"></textarea></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="vendor">Vendor </label></th>
		<td><input name="vendor" type="text" id="vendor" value="" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="region">Region / Country </label></th>
		<td>
            <select name="region" id="region">
                <?php foreach ($config['regions'] as $region): ?>
                <option value="<?php echo $region['slug']; ?>"><?php echo $region['name']; ?></option>
                <?php endforeach; ?>

                <option value=""></option>
                <option value="">Add new region</option>
            </select>
		</td>
	</tr>
    <tr class="form-field">
		<th scope="row"><label for="type">Wine Type </label></th>
		<td>
            <select name="type" id="type">
                <?php foreach ($config['types'] as $type): ?>
                <option value="<?php echo $type['slug']; ?>"><?php echo $type['name']; ?></option>
                <?php endforeach; ?>

                <option value=""></option>
                <option value="">Add new type</option>
            </select>
		</td>
	</tr>
    <tr class="form-field">
		<th scope="row"><label for="price">Price </label></th>
		<td>$ <input name="price" type="text" id="price" value="" /></td>
	</tr>
</table>


<p class="submit"><input type="submit" name="createlabel" id="createlabelsub" class="button button-primary" value="Create Label " /></p>
</form>
<?php
        }
    }

    public function label_maker_builder()
    {
        check_admin_referer('vino100-label-maker');
    }

    protected function in_multiarray($elem, $array)
    {
        foreach ($array as $key => $value)
        {
            if ($value == $elem)
            {
                return true;
            }
            elseif (is_array($value))
            {
                if (self::in_multiarray($elem, $value))
                {
                    return true;
                }
            }
        }

        return false;
    }
}

$tb_label_maker = new TB_Label_Maker;

function tb_activate()
{
    include(TB_PLUGIN_DIR . 'installer.php');

    tb_install();
}
register_activation_hook(__FILE__, 'tb_activate');

function tb_deactivate()
{
    include(TB_PLUGIN_DIR . 'installer.php');

    tb_uninstall();
}
register_deactivation_hook(__FILE__, 'tb_deactivate');

/* EOF */
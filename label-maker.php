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
        $page = add_management_page('Vino Label Maker', 'Vino Label Maker', 'publish_pages', 'vino-label-maker', array($this, 'label_maker_form'));
    
        add_action('admin_print_styles-' . $page, array($this, 'admin_styles'));
        add_action('admin_print_scripts-' . $page, array($this, 'admin_scripts'));
    }

    public function admin_styles()
    {
        wp_enqueue_style('vino100-styles', TB_PLUGIN_URL . 'style.css');
    }

    public function admin_scripts()
    {
        echo '<script type="text/javascript">var vino100_plugin_url = "' . TB_PLUGIN_URL . '";</script>';
 
        wp_enqueue_script('vino100-meters', TB_PLUGIN_URL . 'meters.js', array(), false, true);
        wp_enqueue_script('vino100-otherize', TB_PLUGIN_URL . 'jquery.otherize.js');
    }

    public function label_maker_form()
    {
        $config = get_option('tb_label_maker_config');

        if (isset($_POST['createlabel']))
        {
            self::save_new_options();
            self::build_label();
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
        <th scope="row"><label for="year">Year </label></th>
        <td><input name="year" type="text" id="year" value="" /></td>
    </tr>
    <tr class="form-field">
		<th scope="row"><label for="type">Wine Type </label></th>
		<td>
            <select name="type" id="type">
                <?php foreach ($config['types'] as $type): ?>
                <option value="<?php echo $type['slug']; ?>"><?php echo $type['name']; ?></option>
                <?php endforeach; ?>

                <option value="" disabled="disabled"></option>
            </select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="region">Region </label></th>
		<td>
            <select name="region" id="region">
                <?php foreach ($config['regions'] as $region): ?>
                <option value="<?php echo $region['slug']; ?>"><?php echo $region['name']; ?></option>
                <?php endforeach; ?>

                <option value="" disabled="disabled"></option>
            </select>
		</td>
	</tr>
    <tr class="form-field">
		<th scope="row"><label for="country">Country / State </label></th>
		<td>
            <select name="country" id="country">
                <?php foreach ($config['countries'] as $type): ?>
                <option value="<?php echo $type['slug']; ?>"><?php echo $type['name']; ?></option>
                <?php endforeach; ?>

                <option value="" disabled="disabled"></option>
            </select>
		</td>
	</tr>
    <tr class="form-field">
		<th scope="row"><label for="price">Price </label></th>
		<td>$ <input name="price" type="text" id="price" value="" /></td>
	</tr>
    <tr class="form-field">
        <th scope="row"><label for="ratings">Ratings </label></th>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" style="font-family:arial; font-size:11px; color:#000000; width:244px;">
                <tr>
                    <td align="left" width="25%"><em>Fruity</em></td>
                    <td align="center" width="50%"><strong>FLAVOR METER</strong></td>
                    <td align="right" width="25%"><em>Dry</em></td>
                </tr>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" style="font-family:arial; font-size:11px; font-weight:bold; color:#000000; width:244px;">
                <tr style="cursor:pointer; cursor:hand;">
                    <td><img height="19" name="t1" onclick="javascript:setTopBar(1);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(1);" src="<?php echo TB_PLUGIN_URL; ?>img/white_1.gif" width="18" /></td>
                    <td><img height="19" name="t2" onclick="javascript:setTopBar(2);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(2);" src="<?php echo TB_PLUGIN_URL; ?>img/white_2.gif" width="18" /></td>
                    <td><img height="19" name="t3" onclick="javascript:setTopBar(3);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(3);" src="<?php echo TB_PLUGIN_URL; ?>img/white_3.gif" width="18" /></td>
                    <td><img height="19" name="t4" onclick="javascript:setTopBar(4);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(4);" src="<?php echo TB_PLUGIN_URL; ?>img/white_4.gif" width="18" /></td>
                    <td><img height="19" name="t5" onclick="javascript:setTopBar(5);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(5);" src="<?php echo TB_PLUGIN_URL; ?>img/white_5.gif" width="18" /></td>
                    <td><img height="19" name="t6" onclick="javascript:setTopBar(6);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(6);" src="<?php echo TB_PLUGIN_URL; ?>img/white_6.gif" width="18" /></td>
                    <td><img height="19" name="t7" onclick="javascript:setTopBar(7);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(7);" src="<?php echo TB_PLUGIN_URL; ?>img/white_7.gif" width="18" /></td>
                    <td><img height="19" name="t8" onclick="javascript:setTopBar(8);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(8);" src="<?php echo TB_PLUGIN_URL; ?>img/white_8.gif" width="18" /></td>
                    <td><img height="19" name="t9" onclick="javascript:setTopBar(9);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(9);" src="<?php echo TB_PLUGIN_URL; ?>img/white_9.gif" width="18" /></td>
                    <td><img height="19" name="t10" onclick="javascript:setTopBar(10);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(10);" src="<?php echo TB_PLUGIN_URL; ?>img/white_10.gif" width="18" /></td>
                    <td><img height="19" name="t11" onclick="javascript:setTopBar(11);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(11);" src="<?php echo TB_PLUGIN_URL; ?>img/white_11.gif" width="18" /></td>
                    <td><img height="19" name="t12" onclick="javascript:setTopBar(12);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(12);" src="<?php echo TB_PLUGIN_URL; ?>img/white_12.gif" width="18" /></td>
                    <td><img height="19" name="t13" onclick="javascript:setTopBar(13);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(13);" src="<?php echo TB_PLUGIN_URL; ?>img/white_13.gif" width="18" /></td>
                    <td><img height="19" name="t14" onclick="javascript:setTopBar(14);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(14);" src="<?php echo TB_PLUGIN_URL; ?>img/white_14.gif" width="18" /></td>
                </tr>

                <tr align="center" style="cursor:pointer; cursor:hand;" valign="middle">
                    <td onclick="javascript:setTopBar(1);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(1);">1</td>
                    <td onclick="javascript:setTopBar(2);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(2);">2</td>
                    <td onclick="javascript:setTopBar(3);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(3);">3</td>
                    <td onclick="javascript:setTopBar(4);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(4);">4</td>
                    <td onclick="javascript:setTopBar(5);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(5);">5</td>
                    <td onclick="javascript:setTopBar(6);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(6);">6</td>
                    <td onclick="javascript:setTopBar(7);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(7);">7</td>
                    <td onclick="javascript:setTopBar(8);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(8);">8</td>
                    <td onclick="javascript:setTopBar(9);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(9);">9</td>
                    <td onclick="javascript:setTopBar(10);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(10);">10</td>
                    <td onclick="javascript:setTopBar(11);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(11);">11</td>
                    <td onclick="javascript:setTopBar(12);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(12);">12</td>
                    <td onclick="javascript:setTopBar(13);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(13);">13</td>
                    <td onclick="javascript:setTopBar(14);" onmouseout="javascript:revTopBar();" onmouseover="javascript:topBar(14);">14</td>
                </tr>
            </table>

            <img height="5" src="dot.gif" width="1" /><br />

            <table border="0" cellpadding="0" cellspacing="0" style="font-family:arial; font-size:11px; color:#000000; width:244px;">
                <tr>
                    <td align="left" width="25%"><em>Light</em></td>
                    <td align="center" width="50%"><strong>BODY METER</strong></td>
                    <td align="right" width="25%"><em>Full</em></td>
                </tr>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" style="font-family:arial; font-size:11px; font-weight:bold; color:#000000; width:244px;">
                <tr style="cursor:pointer; cursor:hand;">
                    <td><img height="19" name="b1" onclick="javascript:setBotBar(1);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(1);" src="<?php echo TB_PLUGIN_URL; ?>img/white_1.gif" width="18" /></td>
                    <td><img height="19" name="b2" onclick="javascript:setBotBar(2);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(2);" src="<?php echo TB_PLUGIN_URL; ?>img/white_2.gif" width="18" /></td>
                    <td><img height="19" name="b3" onclick="javascript:setBotBar(3);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(3);" src="<?php echo TB_PLUGIN_URL; ?>img/white_3.gif" width="18" /></td>
                    <td><img height="19" name="b4" onclick="javascript:setBotBar(4);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(4);" src="<?php echo TB_PLUGIN_URL; ?>img/white_4.gif" width="18" /></td>
                    <td><img height="19" name="b5" onclick="javascript:setBotBar(5);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(5);" src="<?php echo TB_PLUGIN_URL; ?>img/white_5.gif" width="18" /></td>
                    <td><img height="19" name="b6" onclick="javascript:setBotBar(6);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(6);" src="<?php echo TB_PLUGIN_URL; ?>img/white_6.gif" width="18" /></td>
                    <td><img height="19" name="b7" onclick="javascript:setBotBar(7);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(7);" src="<?php echo TB_PLUGIN_URL; ?>img/white_7.gif" width="18" /></td>
                    <td><img height="19" name="b8" onclick="javascript:setBotBar(8);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(8);" src="<?php echo TB_PLUGIN_URL; ?>img/white_8.gif" width="18" /></td>
                    <td><img height="19" name="b9" onclick="javascript:setBotBar(9);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(9);" src="<?php echo TB_PLUGIN_URL; ?>img/white_9.gif" width="18" /></td>
                    <td><img height="19" name="b10" onclick="javascript:setBotBar(10);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(10);" src="<?php echo TB_PLUGIN_URL; ?>img/white_10.gif" width="18" /></td>
                    <td><img height="19" name="b11" onclick="javascript:setBotBar(11);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(11);" src="<?php echo TB_PLUGIN_URL; ?>img/white_11.gif" width="18" /></td>
                    <td><img height="19" name="b12" onclick="javascript:setBotBar(12);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(12);" src="<?php echo TB_PLUGIN_URL; ?>img/white_12.gif" width="18" /></td>
                    <td><img height="19" name="b13" onclick="javascript:setBotBar(13);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(13);" src="<?php echo TB_PLUGIN_URL; ?>img/white_13.gif" width="18" /></td>
                    <td><img height="19" name="b14" onclick="javascript:setBotBar(14);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(14);" src="<?php echo TB_PLUGIN_URL; ?>img/white_14.gif" width="18" /></td>
                </tr>

                <tr align="center" style="cursor:pointer; cursor:hand;" valign="middle">
                    <td onclick="javascript:setBotBar(1);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(1);">1</td>
                    <td onclick="javascript:setBotBar(2);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(2);">2</td>
                    <td onclick="javascript:setBotBar(3);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(3);">3</td>
                    <td onclick="javascript:setBotBar(4);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(4);">4</td>
                    <td onclick="javascript:setBotBar(5);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(5);">5</td>
                    <td onclick="javascript:setBotBar(6);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(6);">6</td>
                    <td onclick="javascript:setBotBar(7);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(7);">7</td>
                    <td onclick="javascript:setBotBar(8);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(8);">8</td>
                    <td onclick="javascript:setBotBar(9);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(9);">9</td>
                    <td onclick="javascript:setBotBar(10);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(10);">10</td>
                    <td onclick="javascript:setBotBar(11);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(11);">11</td>
                    <td onclick="javascript:setBotBar(12);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(12);">12</td>
                    <td onclick="javascript:setBotBar(13);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(13);">13</td>
                    <td onclick="javascript:setBotBar(14);" onmouseout="javascript:revBotBar();" onmouseover="javascript:botBar(14);">14</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p class="submit"><input type="submit" name="createlabel" id="createlabelsub" class="button button-primary" value="Create Label " /></p>
</form>

<script type="text/javascript">
jQuery("#type").otherize("Add new type");
jQuery("#region").otherize("Add new region");
jQuery("#country").otherize("Add new country");
</script>
<?php
        }
    }

    public function label_maker_builder()
    {
        check_admin_referer('vino100-label-maker');
    }

    protected function save_new_options()
    {
        if (!$this->in_multiarray($_POST['country']))
        {
            $config['countries'][] = array(
                'slug'  => strtolower(str_replace(' ', '_', $_POST['country'])),
                'name'  => $_POST['country'],
            );
        }

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

    protected function build_label()
    {
        
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
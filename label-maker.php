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
    protected $post_type = 'wine_label';

    /**
     * Plugin constructor. Runs all of the main methods to start the plugin.
     *
     * @access public
     *
     * @return void
     */
    public function __construct()
    {
        add_action('init', array($this, 'register_cpt_wine_label'));

        if (is_admin())
        {
            add_action('save_post', array($this, 'wine_label_form_mb_save'));

            add_action('admin_head', array($this, 'admin_head'));
            add_action('admin_init', array($this, 'admin_init'));

            add_action('admin_head-post.php', array($this, 'cpt_hide_publishing_actions'));
            add_action('admin_head-post-new.php', array($this, 'cpt_hide_publishing_actions'));
            add_action('post_submitbox_misc_actions', array($this, 'cpt_custom_publishing_actions'));
            add_filter('gettext', array($this, 'cpt_change_publish_button'), 10, 2);

            // admin actions/filters
            add_action('admin_footer-edit.php', array($this, 'custom_bulk_admin_footer'));
            add_action('load-edit.php', array($this, 'custom_bulk_action'));
        }

        add_action('admin_menu', array($this, 'admin_menu'));
    }

    public function register_cpt_wine_label()
    {
        $labels = array(
            'name'                  => _x('Wine Labels', 'wine_label'),
            'singular_name'         => _x('Wine Label', 'wine_label'),
            'add_new'               => _x('Add New', 'wine_label'),
            'add_new_item'          => _x('Add New Wine Label', 'wine_label'),
            'edit_item'             => _x('Edit Wine Label', 'wine_label'),
            'new_item'              => _x('New Wine Label', 'wine_label'),
            'view_item'             => _x('View Wine Label', 'wine_label'),
            'search_items'          => _x('Search Wine Labels', 'wine_label'),
            'not_found'             => _x('No wine labels found', 'wine_label'),
            'not_found_in_trash'    => _x('No wine labels found in Trash', 'wine_label'),
            'parent_item_colon'     => _x('Parent Wine Label:', 'wine_label'),
            'menu_name'             => _x('Wine Labels', 'wine_label'),
        );

        $args = array(
            'labels'                => $labels,
            'hierarchical'          => false,

            'supports'              => array('title'),
            'register_meta_box_cb'  => array($this, 'cpt_meta_boxes_cb'),

            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 100,

            'show_in_nav_menus'     => false,
            'publicly_queryable'    => false,
            'exclude_from_search'   => true,
            'has_archive'           => false,
            'query_var'             => false,
            'can_export'            => true,
            'rewrite'               => false,
            'capability_type'       => 'post',
        );

        register_post_type($this->post_type, $args);
    }

    public function cpt_meta_boxes_cb()
    {
        add_meta_box('wine-label-form', 'Wine Label', array($this, 'wine_label_form_mb'), $this->post_type, 'normal', 'high');
    }

    public function wine_label_form_mb($post)
    {

        $meta = get_post_meta($post->ID, 'wine_data', true);

        if (empty($meta))
        {
            $meta = array(
                'desc'      => '',
                'vendor'    => '',
                'year'      => '',
                'type'      => '',
                'region'    => '',
                'country'   => '',
                'price'     => '',
                'flavor'    => '',
                'body'      => '',
            );
        }

        ?>
<table class="form-table">
    <?php wp_nonce_field('wine_label_maker', 'wine_label_maker'); ?>

	<tr class="form-field">
		<th scope="row"><label for="product_desc">Description </label></th>
		<td><textarea name="desc" id="product_desc" rows="5" cols="30"><?php echo $meta['desc']; ?></textarea></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="vendor">Vendor </label></th>
		<td><input name="vendor" type="text" id="vendor" value="<?php echo $meta['vendor']; ?>" /></td>
	</tr>
    <tr class="form-field">
        <th scope="row"><label for="year">Year </label></th>
        <td><input name="year" type="text" id="year" value="<?php echo $meta['year']; ?>" /></td>
    </tr>
    <tr class="form-field">
		<th scope="row"><label for="type">Wine Type </label></th>
		<td>
            <select name="type" id="type">
                <option value=""<?php selected('', $meta['type']); ?>>-</option>
                <?php foreach ($this->config['types'] as $type): ?>
                <option value="<?php echo $type['slug']; ?>"<?php selected($type['slug'], $meta['type']); ?>><?php echo $type['name']; ?></option>
                <?php endforeach; ?>

                <option value="" disabled="disabled"></option>
            </select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="region">Region </label></th>
		<td>
            <select name="region" id="region">
                <option value=""<?php selected('', $meta['region']); ?>>-</option>
                <?php foreach ($this->config['regions'] as $region): ?>
                <option value="<?php echo $region['slug']; ?>"<?php selected($region['slug'], $meta['region']); ?>><?php echo $region['name']; ?></option>
                <?php endforeach; ?>

                <option value="" disabled="disabled"></option>
            </select>
		</td>
	</tr>
    <tr class="form-field">
		<th scope="row"><label for="country">Country / State </label></th>
		<td>
            <select name="country" id="country">
                <option value=""<?php selected('', $meta['country']); ?>>-</option>
                <?php foreach ($this->config['countries'] as $country): ?>
                <option value="<?php echo $country['slug']; ?>"<?php selected($country['slug'], $meta['country']); ?>><?php echo $country['name']; ?></option>
                <?php endforeach; ?>

                <option value="" disabled="disabled"></option>
            </select>
		</td>
	</tr>
    <tr class="form-field">
		<th scope="row"><label for="price">Price </label></th>
		<td>$ <input name="price" type="text" id="price" value="<?php echo $meta['price']; ?>" /></td>
	</tr>
    <tr class="form-field">
        <th scope="row"><label for="ratings">Ratings </label></th>
        <td>
            <input type="hidden" name="wine_flavor" value="<?php echo $meta['flavor']; ?>" />
            <input type="hidden" name="wine_body" value="<?php echo $meta['body']; ?>" />

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

<?php if (!empty($meta['flavor'])): ?><script type="text/javascript">setTopBar(<?php echo $meta['flavor']; ?>);</script><?php endif; ?>
<?php if (!empty($meta['body'])): ?><script type="text/javascript">setBotBar(<?php echo $meta['body']; ?>);</script><?php endif; ?>

<script type="text/javascript">
jQuery("#type").otherize("Add new type");
jQuery("#region").otherize("Add new region");
jQuery("#country").otherize("Add new country");
</script>
<?php
    }

    public function wine_label_form_mb_save($post_id)
    {
        // Check if it is for our CPT and we are authorized
        if (!isset($_POST['wine_label_maker'])
            || !check_admin_referer('wine_label_maker', 'wine_label_maker')
            || !current_user_can('edit_post', $post_id))
        {
            return $post_id;
        }

        // Don't duplicate data
        if ($_POST['post_type'] == 'revision')
        {
            return;
        }

        // Save the info
        $this->save_new_options();

        $wine_meta = array(
            'desc'      => $_POST['desc'],
            'vendor'    => $_POST['vendor'],
            'year'      => $_POST['year'],
            'type'      => $_POST['type'],
            'region'    => $_POST['region'],
            'country'   => $_POST['country'],
            'price'     => $_POST['price'],
            'flavor'    => $_POST['wine_flavor'],
            'body'      => $_POST['wine_body'],
        );

        if (get_post_meta($post_id, 'wine_data', false))
        {
            update_post_meta($post_id, 'wine_data', $wine_meta);
        }
        else
        {
            add_post_meta($post_id, 'wine_data', $wine_meta);
        }
    }

    public function admin_head()
    {
        global $post_type;

        if ((isset($_GET['post_type']) && $_GET['post_type'] == $this->post_type) || ($post_type == $this->post_type))
        {
            echo '<link type="text/css" rel="stylesheet" href="' . TB_PLUGIN_URL . 'style.css" />';
            echo '<script type="text/javascript">var vino100_plugin_url = "' . TB_PLUGIN_URL . '";</script>';
            echo '<script type="text/javascript" src="' . TB_PLUGIN_URL . 'js/meters.js"></script>';
            echo '<script type="text/javascript" src="' . TB_PLUGIN_URL . 'js/jquery.otherize.js"></script>';
        }
    }

    public function admin_init()
    {
        $this->config = get_option('tb_label_maker_config');

        if (isset($_GET['page']) && $_GET['page'] == 'print-labels')
        {
            ob_start();
        }
    }

    public function cpt_hide_publishing_actions()
    {
        global $post;

        if ($post->post_type == $this->post_type)
        {
            echo '<style type="text/css">
            .misc-pub-post-status, .misc-pub-visibility, .misc-pub-curtime, #minor-publishing-actions { display:none; }
            #misc-publishing-actions { padding-top: 0; }
            </style>';
        }
    }

    public function cpt_custom_publishing_actions()
    {
        global $post;

        if ($post->post_type == $this->post_type && (isset($_GET['action']) && $_GET['action'] == 'edit'))
        {
            echo '<div class="misc-pub-section misc-pub-print"><a href="' . admin_url('edit.php?post_type=wine_label&paged=1&page=print-labels&ids='.$post->ID) .'">Print</a></div>';
        }
    }

    public function cpt_change_publish_button($translation, $text)
    {
        if ($this->post_type == get_post_type() && 'Publish' == $text)
        {
            return 'Save';
        }

        return $translation;
    }

    public function custom_bulk_admin_footer()
    {
        global $post_type;

        if ($post_type == $this->post_type)
        {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('<option>').val('print').text('<?php _e('Print')?>').appendTo("select[name='action']");
                    jQuery('<option>').val('print').text('<?php _e('Print')?>').appendTo("select[name='action2']");
                });
            </script>
            <?php
        }
    }

    public function custom_bulk_action()
    {
        global $typenow;
        $post_type = $typenow;

        if ($post_type == $this->post_type)
        {
            // get the action
            $wp_list_table = _get_list_table('WP_Posts_List_Table');  // depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc
            $action = $wp_list_table->current_action();

            $allowed_actions = array('print');
            if (!in_array($action, $allowed_actions))
            {
                return;
            }

            // security check
            check_admin_referer('bulk-posts');

            // make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
            if (isset($_REQUEST['post']))
            {
                $post_ids = array_map('intval', $_REQUEST['post']);
            }

            if (empty($post_ids))
            {
                return;
            }

            // this is based on wp-admin/edit.php
            $sendback = remove_query_arg(array('printed', 'untrashed', 'deleted', 'ids'), wp_get_referer());
            if (!$sendback)
            {
                $sendback = admin_url('edit.php?post_type=' . $post_type);
            }

            $pagenum = $wp_list_table->get_pagenum();
            $sendback = add_query_arg('paged', $pagenum, $sendback);

            switch ($action)
            {
                case 'print':
                    $sendback = add_query_arg(array('page' => 'print-labels', 'ids' => join(',', $post_ids)), $sendback);
                break;

                default:
                    return;
            }

            $sendback = remove_query_arg(array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback);

            wp_redirect($sendback);
            exit();
        }
    }

    public function admin_menu()
    {
        $page = add_submenu_page('edit.php?post_type=wine_label', 'Print Labels', 'Print Labels', 'publish_posts', 'print-labels', array($this, 'print_labels'));

        add_action('admin_print_styles-' . $page, array($this, 'admin_styles'));
    }

    public function admin_styles()
    {
        wp_enqueue_style('wine-label-print-styles', TB_PLUGIN_URL . 'print.css');
    }

    public function admin_scripts()
    {
        echo '<script type="text/javascript">var vino100_plugin_url = "' . TB_PLUGIN_URL . '";</script>';

        wp_enqueue_script('vino100-meters', TB_PLUGIN_URL . 'meters.js', array(), false, true);
        wp_enqueue_script('vino100-otherize', TB_PLUGIN_URL . 'jquery.otherize.js');
    }

    public function print_labels()
    {
        $labels = get_posts(array(
            'include'       => $_GET['ids'],
            'post_type'     => 'wine_label',
            'post_status'   => 'publish',
        ));

?>
<table>
    <?php foreach ($labels as $label):
        $meta = get_post_meta($label->ID, 'wine_data', true); ?>
    <tr>
        <td class="label">
            <div class="fill">
                <div class="logo">
                    <span><img class="logo-resize" src="<?php echo TB_PLUGIN_URL; ?>img/vino-logo.png" /></span>
                    <span class="right">
                        <div class="type bold text-center"><?php if (!empty($meta['type'])) { echo $this->get_option_name('types', $meta['type']); } ?></div>
                        <!--<br class="clear" />-->
                        <div class="region bold text-center"><?php
                            if (!empty($meta['region']))
                            {
                                echo $this->get_option_name('regions', $meta['region']);
                            }

                            if (!empty($meta['region']) && !empty($meta['country']))
                            {
                                echo ', ';
                            }

                            if (!empty($meta['country']))
                            {
                                echo $this->get_option_name('countries', $meta['country']);
                            }
                        ?></div>
                    </span>
                </div>
                <h3 class="vendor text-center"><?php if (!empty($meta['vendor'])) { echo $meta['vendor']; } else { echo '&nbsp;'; } ?></h3>
                <h3 class="title text-center"><?php
                    if (!empty($label->post_title))
                    {
                        echo $label->post_title;
                    }

                    if (!empty($meta['year']))
                    {
                        echo ' '.$meta['year'];
                    }

                    if (empty($label->post_title) && empty($meta['year']))
                    {
                        echo '&nbsp;';
                    }
                    ?></h3>
                <?php if (!empty($meta['desc'])): ?><p class="desc text-center"><?php echo $meta['desc']; ?></p><?php endif; ?>
            </div>
            <div class="footer">
                <span class="meters">
                    <table class="meter-display" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="left" width="25%"><em>Fruity</em></td>
                            <td align="center" width="50%"><strong>Flavor</strong></td>
                            <td align="right" width="25%"><em>Dry</em></td>
                        </tr>
                    </table>

                    <table class="meter-display bold" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <?php
                            for ($i = 1; $i <= 14; $i++)
                            {
                                if (!empty($meta['flavor']))
                                {
                                    $color = ($i <= $meta['flavor']) ? 'red' : 'white';
                                }
                                else
                                {
                                    $color = 'white';
                                }

                                echo '<td><img width="18" height="14" src="' . TB_PLUGIN_URL . 'img/' . $color . '_' . $i . '.gif" /></td>';
                            }
                            ?>
                        </tr>
                    </table>

                    <table class="meter-display" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="left" width="25%"><em>Light</em></td>
                            <td align="center" width="50%"><strong>Body</strong></td>
                            <td align="right" width="25%"><em>Full</em></td>
                        </tr>
                    </table>

                    <table class="meter-display bold" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <?php
                            for ($i = 1; $i <= 14; $i++)
                            {
                                if (!empty($meta['body']))
                                {
                                    $color = ($i <= $meta['body']) ? 'red' : 'white';
                                }
                                else
                                {
                                    $color = 'white';
                                }

                                echo '<td><img width="18" height="14" src="' . TB_PLUGIN_URL . 'img/' . $color . '_' . $i . '.gif" /></td>';
                            }
                            ?>
                        </tr>
                    </table>
                </span>
                <span class="price right bold"><?php if (!empty($meta['price'])) { echo '$' . $meta['price']; } ?></span>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php
    }

    protected function save_new_options()
    {
        if (!$this->in_multiarray($_POST['country'], $this->config['countries']))
        {
            $this->config['countries'][] = array(
                'slug'  => strtolower(str_replace(' ', '_', $_POST['country'])),
                'name'  => $_POST['country'],
            );
        }

        if (!$this->in_multiarray($_POST['region'], $this->config['regions']))
        {
            $this->config['regions'][] = array(
                'slug'  => strtolower(str_replace(' ', '_', $_POST['region'])),
                'name'  => $_POST['region'],
            );
        }

        if (!$this->in_multiarray($_POST['type'], $this->config['types']))
        {
            $this->config['types'][] = array(
                'slug'  => strtolower(str_replace(' ', '_', $_POST['type'])),
                'name'  => $_POST['type'],
            );
        }

        update_option('tb_label_maker_config', $this->config);
    }

    protected function get_option_name($class, $slug)
    {
        if (!isset($this->config[$class]))
        {
            // We don't have this in our options
            return '';
        }

        foreach ($this->config[$class] as $i => $ary)
        {
            if ($this->config[$class][$i]['slug'] == $slug)
            {
                return $this->config[$class][$i]['name'];
            }
        }

        return '';
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
                if ($this->in_multiarray($elem, $value))
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
<?php
if (isset($_POST['spr_reset_votes']))
{
    if (current_user_can('activate_plugins'))
    {
        spr_truncate_tables();
    }
}

spr_save_settings();
$options=spr_options();
wp_enqueue_style('farbtastic');
wp_enqueue_script('farbtastic');
wp_enqueue_style('spr_style', plugins_url('/resources/spr_style.css', __FILE__));
wp_enqueue_script('spr_admin', plugins_url('/resources/spr_admin.js', __FILE__), array('farbtastic', 'jquery'), NULL);
wp_localize_script('spr_admin', 'spr_ajax_object', array('scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape']));
?>
<h1>Adjust settings of the Simple Rating</h1>
<div style="float:left;">
    <form name="form" method="POST" style="margin-top:15px;">
        <table>
            <tr>
                <td  width="150px"><label>Show rating</label></td>
                <td><input type="checkbox" name="spr_activated" id="spr_activated" value="<?php echo $options['activated']; ?>" <?php checked($options['activated'], 1, true); ?>></td>
            </tr>
            <tr>
                <td  width="150px"><label>Insertion method</label></td>
                <td>
                    <select name="spr_method" id="spr_method" class="spr_admin_input">
                        <option value="auto" <?php selected($options['method'], 'auto', true); ?>>Automatic</option>
                        <option value="manual" <?php selected($options['method'], 'manual', true); ?>>Manual</option>
                    </select>
                    <?php if ($options['method']=='manual')
                    {
                        ?><span id="spr_method_hint"  style="display:inline;">Insert &#60;?php echo spr_show_rating();?&#62; where you need it.</span><?php }
                else
                { ?>
                        <span id="spr_method_hint">Insert &#60;?php echo spr_show_rating();?&#62; where you need it.</span>
<?php } ?>
                </td>
            </tr> 
            <tr>
                <td  width="150px"><label>Shape</label></td>
                <td>
                    <select name="spr_shape" id="spr_shape" class="spr_admin_input">
                        <option value="s" <?php selected($options['type'], 's', true); ?>>Stars</option>
                        <option value="c" <?php selected($options['type'], 'c', true); ?>>Circles</option>
                        <option value="h" <?php selected($options['type'], 'h', true); ?>>Hearts</option>
                        <option value="b" <?php selected($options['type'], 'b', true); ?>>Bar</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td  width="150px"><label>Alignment</label></td>
                <td>
                    <select name="spr_alignment" id="spr_alignment" class="spr_admin_input">
                        <option value="center" <?php selected($options['alignment'], 'center', true); ?>>Center</option>
                        <option value="right" <?php selected($options['alignment'], 'right', true); ?>>Right</option>
                        <option value="left" <?php selected($options['alignment'], 'left', true); ?>>Left</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td  width="150px"><label>Color</label></td>
                <td>
                    <select name="spr_color" id="spr_color" class="spr_admin_input">
                        <option value="y" <?php selected($options['color'], 'y', true); ?>>Yellow</option>
                        <option value="p" <?php selected($options['color'], 'p', true); ?>>Purple</option>
                        <option value="g" <?php selected($options['color'], 'g', true); ?>>Green</option>
                        <option value="b" <?php selected($options['color'], 'b', true); ?>>Blue</option>
                        <option value="r" <?php selected($options['color'], 'r', true); ?>>Red</option>
                    </select>
                </td>
            </tr>   
            <tr>
                <td  width="150px"><label>Show vote count</label></td>
                <td><input type="checkbox" name="spr_show_vote_count" id="spr_show_vote_count" value="<?php echo $options['show_vote_count']; ?>" <?php checked($options['show_vote_count'], 1, true); ?>></td>
            </tr>
            <tr>
                <td  width="150px"><label>Vote count color</label></td>
                <td>
                    <input type="text" size="10" maxlength="8" name="spr_vote_count_color" id="spr_vote_count_color" value="<?php echo $options['vote_count_color']; ?>" class="spr_admin_input">
                    <a href="#" id="spr_vote_count_color_box" class="pickcolor" style="padding: 4px 11px; border: 1px solid #dfdfdf; margin: 0 7px 0 3px; background-color: <?php echo $options['vote_count_color']; ?>;"></a>
                    <div id="psr_color_picker" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
                </td>
            </tr>
            <tr>
                <td  width="150px"><label>Vote count style</label></td>
                <td> 
                    Bold <input type="checkbox" name="spr_vc_bold" id="spr_vc_bold" value="<?php echo $options['vc_bold']; ?>" <?php checked($options['vc_bold'], 1, true); ?>>
                    Italic <input type="checkbox" name="spr_vc_italic" id="spr_vc_italic" value="<?php echo $options['vc_italic']; ?>" <?php checked($options['vc_italic'], 1, true); ?>>
                </td>
            </tr>
            <td  width="150px"><label>Scale</label></td>
            <td><input type="text" size="10" maxlength="200" name="spr_scale" id="spr_scale" value="<?php echo $options['scale']; ?>" class="spr_admin_input"></td>
            </tr>
            <tr>
                <td  width="150px"><label>Where to add rating</label></td>
                <td>
                    <?php echo spr_get_post_types_fo();?>
                </td>
            </tr> 
            <tr>
                <td  width="150px"><label>Show in loops</label></td>
                <td><input type="checkbox" name="spr_show_in_loops" id="spr_show_in_loops" value="<?php echo $options['show_in_loops']; ?>" <?php checked($options['show_in_loops'], 1, true); ?>></td>
            </tr>
            <tr>
                <td  width="150px"><label>Position</label></td>
                <td>
                    <select name="spr_position" id="spr_position" class="spr_admin_input">
                        <option value="before" <?php selected($options['position'], 'before', true); ?>>Before content</option>
                        <option value="after" <?php selected($options['position'], 'after', true); ?>>After content</option>
                    </select>
                </td>
            </tr> 
        </table>
        <input type="submit" style="margin-top:10px;" class='button button-primary button-large' value="Save settings">
    </form>
</div>
<div id="postbox-container-1" class="postbox-container" style="float: right;display:inline-block;width: 280px;margin-right:20px;">
    <div id="postimagediv" class="postbox ">
        <h3 class="spr_widget_title">
            <span>Live preview</span>
        </h3>
        <div class="inside">         
            <div id="spr_container"><div id="spr_visual_container"><?php echo spr_show_voting(5, 25); ?></div></div>
        </div>
    </div>
    <div id="postimagediv" class="postbox ">
        <h3 class="spr_widget_title">
            <span>Donate</span>
        </h3>
        <div class="inside">         
            <form action="https://www.moneybookers.com/app/payment.pl" method="post">
                <input type="hidden" name="pay_to_email" value="igor.yavych@gmail.com">
                <input type="hidden" name="status_url" value="mailto:igor.yavych@gmail.com">
                <input type="hidden" name="language" value="EN">
                <input type="hidden" name="recipient_description" value="Simple Rating">
                <input type="text" name="amount" size="5"  value="5" />
                <select name="currency" id="currency">
                    <option value="USD" selected="selected">USD</option>
                    <option value="EUR">EUR</option>
                </select>
                <input type="hidden" name="confirmation_note" value="Thanks for your support!">
                <br/><input class="spr_button button button-primary button-small" type="submit" value="Donate via Skrill">
            </form>
        </div>
    </div>
    <div id="postimagediv" class="postbox ">
        <h3 class="spr_widget_title">
            <span>Reset votes</span>
        </h3>
        <div class="inside">         
            <form method="post" onsubmit="return confirm('Do you really want to reset votes?')">
                You can reset votes by pressing button below.<br/>
                <input type="hidden" name="spr_reset_votes" value="1">
                <input class="spr_button button button-primary button-small" type="submit" value="Reset votes">
            </form>
        </div>
    </div>
</div>
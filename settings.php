<?php 
spr_save_settings();
$options=spr_options(); 
wp_enqueue_style('spr_style', plugins_url('/resources/spr_style.css', __FILE__));
wp_enqueue_script('spr_admin', plugins_url('/resources/spr_admin.js', __FILE__), array('jquery'), NULL);
wp_localize_script('spr_admin', 'spr_ajax_object', array('scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape']));
?>
 <h1>Adjust settings of the Simple Rating</h1>
 <div style="float:left;">
 <form name="form" method="POST" style="margin-top:15px;">
   <table>
        <tr>
            <td  width="150px"><label>Show rating</label></td>
            <td><input type="checkbox" name="spr_activated" id="spr_activated" value="<?php echo $options['activated'];?>" <?php checked( $options['activated'], 1,true ); ?>></td>
        </tr>
        <tr>
            <td  width="150px"><label>Shape</label></td>
            <td>
                <select name="spr_shape" id="spr_shape">
                    <option value="s" <?php selected( $options['type'], 's', true); ?>>Stars</option>
                    <option value="c" <?php selected( $options['type'], 'c', true); ?>>Circles</option>
                    <option value="h" <?php selected( $options['type'], 'h', true); ?>>Hearts</option>
                    <option value="b" <?php selected( $options['type'], 'b', true); ?>>Bar</option>
                </select>
            </td>
        </tr>
        <tr>
            <td  width="150px"><label>Color</label></td>
            <td>
                <select name="spr_color" id="spr_color">
                    <option value="y" <?php selected( $options['color'], 'y', true); ?>>Yellow</option>
                    <option value="p" <?php selected( $options['color'], 'p', true); ?>>Purple</option>
                    <option value="g" <?php selected( $options['color'], 'g', true); ?>>Green</option>
                    <option value="b" <?php selected( $options['color'], 'b', true); ?>>Blue</option>
                    <option value="r" <?php selected( $options['color'], 'r', true); ?>>Red</option>
                </select>
            </td>
        </tr>   
        <tr>
            <td  width="150px"><label>Show vote count</label></td>
            <td><input type="checkbox" name="spr_show_vote_count" id="spr_show_vote_count" value="<?php echo $options['show_vote_count'];?>" <?php checked( $options['show_vote_count'], 1,true ); ?>></td>
        </tr>
        <td  width="150px"><label>Scale</label></td>
        <td><input type="text" size="10" maxlength="200" name="spr_scale" id="spr_scale" value="<?php echo $options['scale'];?>"></td>
        </tr>
        <tr>
            <td  width="150px"><label>Where to add rating</label></td>
            <td>
                <select name="spr_where_to_show" id="spr_where_to_show">
                    <option value="posts" <?php selected( $options['where_to_show'], 'posts', true); ?>>Posts only</option>
                    <option value="pages" <?php selected( $options['where_to_show'], 'pages', true); ?>>Pages only</option>
                    <option value="both" <?php selected( $options['where_to_show'], 'both', true); ?>>Both</option>
                </select>
            </td>
        </tr> 
        <tr>
            <td  width="150px"><label>Position</label></td>
            <td>
                <select name="spr_position" id="spr_position">
                    <option value="before" <?php selected( $options['position'], 'before', true); ?>>Before content</option>
                    <option value="after" <?php selected( $options['position'], 'after', true); ?>>After content</option>
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
       <div id="spr_container"><div id="spr_visual_container"><?php echo spr_show_voting(5, 25);?></div></div>
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
</div>
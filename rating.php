<?php
/*

  Plugin Name: Simple Rating
  Description: Allows users to rate posts and pages.
  Version: 1.3
  Author: Igor Yavych
  Author URI: https://www.odesk.com/users/~~d196de64099a8aa3
 */
require_once ("spr_widgets.php");
require_once ("spr_upgrade.php");
upgrade();
$options=spr_options();
if ($options['activated']==1&&$options['method']=="auto")
{
    add_filter('the_content', 'spr_filter', 15);
}

function spr_filter($content)
{
    $options=spr_options();
    $list=spr_list_cpt_slugs();
    global $post, $wpdb;
    $disable_rating=get_post_meta($post->ID, '_spr_disable', true);
    foreach ($list as $list_)
    {
        if (is_singular($list_)&&$options['where_to_show'][$list_]&&$disable_rating!='1')
        {
            if ($options['position']=='before')
            {
                $content=spr_rating().$content;
            }
            elseif ($options['position']=='after')
            {
                $content .= spr_rating();
            }
            break;
        }
        else if ((is_archive()||(is_home()&&$options['loop_on_hp']==1&&in_the_loop()))&&$options['show_in_loops']==1)
        {
            if ($post->post_type==$list_&$options['where_to_show'][$list_]&&$disable_rating!='1')
            {
                wp_enqueue_style('spr_style', plugins_url('/resources/spr_style.css', __FILE__));
                $query="select `votes`, `points` from `".$wpdb->prefix."spr_rating` where `post_id`='$post->ID';";
                $popularity=$wpdb->get_results($query, ARRAY_N);
                $votes=$popularity[0][0];
                $points=$popularity[0][1];
                $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voted($votes, $points, $options['show_vote_count']).'</div></div>';
                if ($options['position']=='before')
                {
                    $content=$results.$content;
                }
                elseif ($options['position']=='after')
                {
                    $content .= $results;
                }
                break;
            }
        }
    }
    return $content;
}

function spr_show_rating()
{
    $options=spr_options();
    $list=spr_list_cpt_slugs();
    global $post, $wpdb, $spr_added, $spr_added_loop;
    $disable_rating=get_post_meta($post->ID, '_spr_disable', true);
    $result="";
    if ($options['method']=="manual"&&$options['activated']==1)
    {
        foreach ($list as $list_)
        {
            if (is_singular($list_)&&$options['where_to_show'][$list_]&&$disable_rating!='1'&&$spr_added!=1)
            {
                $result=spr_rating();
                $spr_added=1;
                break;
            }
            if ((is_archive()||($options['loop_on_hp']==1&&is_home()&&in_the_loop()))&&$options['show_in_loops']==1)
            {
                if ($post->post_type==$list_&$options['where_to_show'][$list_]&&$disable_rating!='1'&&$spr_added_loop[$post->ID]!=1)
                {
                    wp_enqueue_style('spr_style', plugins_url('/resources/spr_style.css', __FILE__));
                    $query="select `votes`, `points` from `".$wpdb->prefix."spr_rating` where `post_id`='$post->ID';";
                    $popularity=$wpdb->get_results($query, ARRAY_N);
                    $votes=$popularity[0][0];
                    $points=$popularity[0][1];
                    $result='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voted($votes, $points, $options['show_vote_count']).'</div></div>';
                    $spr_added_loop[$post->ID]=1;
                    break;
                }
            }
        }
        return $result;
    }
}

function spr_rating()
{
    global $post, $current_user, $wpdb;
    $query="select `votes`, `points` from `".$wpdb->prefix."spr_rating` where `post_id`='$post->ID';";
    $popularity=$wpdb->get_results($query, ARRAY_N);
    $votes=$popularity[0][0];
    $points=$popularity[0][1];
    wp_enqueue_script('spr_script', plugins_url('/resources/spr_script.js', __FILE__), array('jquery'), NULL);
    wp_enqueue_style('spr_style', plugins_url('/resources/spr_style.css', __FILE__));
    $options=spr_options();
    spr_print_additional_styles();
    if ($votes>0)
    {
        $rate=$points/$votes;
        if ($options['use_aggregated'])
        {
            $aggregated='<div class="spr_hidden" itemscope="" itemtype="http://schema.org/Product"><meta itemprop="name" content="'.$post->post_title.'"><div class="spr_hidden" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating"><meta itemprop="bestRating" content="'.$options['scale'].'"><meta itemprop="ratingValue" content="'.$rate.'"><meta itemprop="ratingCount" content="'.$votes.'"></div></div>';
        }
        else
        {
            $aggregated='';
        }
    }
    else
    {
        $rate=0;
        $votes=0;
        $aggregated='';
    }

    if (is_user_logged_in()==1)
    {
        $query="select * from `".$wpdb->prefix."spr_votes` where `post_id`='$post->ID' and `user_id`='$current_user->ID';";
        $voted=$wpdb->get_results($query, ARRAY_N);
        if (count($voted)>0)
        {
            $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voted($votes, $points, $options['show_vote_count']).'</div></div>'.$aggregated;
            wp_localize_script('spr_script', 'spr_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape'], 'rating_working'=>false, 'post_id'=>$post->ID));
            return $results;
        }
        else
        {
            $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voting($votes, $points, $options['show_vote_count']).'</div></div>'.$aggregated;
            wp_localize_script('spr_script', 'spr_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape'], 'rating_working'=>true, 'post_id'=>$post->ID));
            return $results;
        }
    }
    else if ($options['allow_guest_vote']&&filter_var(spr_get_ip(), FILTER_VALIDATE_IP))
    {
        $query="select * from `".$wpdb->prefix."spr_votes` where `post_id`='$post->ID' and `user_id`='".spr_get_ip()."';";
        $voted=$wpdb->get_results($query, ARRAY_N);
        if (count($voted)>0)
        {
            $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voted($votes, $points, $options['show_vote_count']).'</div></div>'.$aggregated;
            wp_localize_script('spr_script', 'spr_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape'], 'rating_working'=>false, 'post_id'=>$post->ID));
            return $results;
        }
        else
        {
            $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voting($votes, $points, $options['show_vote_count']).'</div></div>'.$aggregated;
            wp_localize_script('spr_script', 'spr_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape'], 'rating_working'=>true, 'post_id'=>$post->ID));
            return $results;
        }
    }
    else
    {
        wp_localize_script('spr_script', 'spr_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape'], 'rating_working'=>false));
        $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voted($votes, $points, $options['show_vote_count']).'</div></div>'.$aggregated;
        return $results;
    }
}

function spr_show_voted($votes, $points, $show_vc)
{
    $options=spr_options();
    $spr_type=$options['color'].$options['shape'];
    if ($votes>0)
    {
        $rate=$points/$votes;
    }
    else
    {
        $rate=0;
        $votes=0;
    }
    $html='<div id="spr_shapes">';
    for ($i=1; $i<=$options['scale']; $i++)
    {
        if ($rate>=($i-0.25))
        {
            $class='spr_'.$spr_type.'_full_voted';
        }
        elseif ($rate<($i-0.25)&&$rate>=($i-0.75))
        {
            $class='spr_'.$spr_type.'_half_voted';
        }
        else
        {
            $class='spr_'.$spr_type.'_empty';
        }
        $html .= '<span class="spr_rating_piece '.$class.'"></span> ';
    }
    $html.='</div>';
    if ($show_vc)
    {
        if ($votes==1)
        {
            $votesorvote='vote';
        }
        else
        {
            $votesorvote='votes';
        }
        $html .= '<span id="spr_votes">'.$votes.' '.$votesorvote.'</span>';
    }

    return $html;
}

function spr_show_voting($votes, $points, $show_vc)
{
    $options=spr_options();
    $spr_type=$options['color'].$options['shape'];
    if ($votes>0)
    {
        $rate=$points/$votes;
    }
    else
    {
        $rate=0;
        $votes=0;
    }
    $html='<div id="spr_shapes">';
    for ($i=1; $i<=$options['scale']; $i++)
    {
        if ($rate>=($i-0.25))
        {
            $class='spr_'.$spr_type.'_full_voting';
        }
        elseif ($rate<($i-0.25)&&$rate>=($i-0.75))
        {
            $class='spr_'.$spr_type.'_half_voting';
        }
        else
        {
            $class='spr_'.$spr_type.'_empty';
        }
        $html .= '<span id="spr_piece_'.$i.'" class="spr_rating_piece '.$class.'"></span> ';
    }
    $html.='</div>';
    if ($show_vc)
    {
        if ($votes==1)
        {
            $votesorvote='vote';
        }
        else
        {
            $votesorvote='votes';
        }
        $html .= '<span id="spr_votes">'.$votes.' '.$votesorvote.'</span>';
    }
    return $html;
}

function spr_rate()
{
    global $current_user, $wpdb;
    $options=spr_options();
    if ($options['activated']==1)
    {
        if (isset($_POST['points'])&&isset($_POST['post_id'])) // key parameters are set
        {
            $post_id=(int) $wpdb->escape($_POST['post_id']);
            $points_=(int) $wpdb->escape($_POST['points']);
            if ($points_>=1&&$points_<=$options['scale'])
            {
                if (is_user_logged_in()==1) // user is logged in
                {
                    $query="select * from `".$wpdb->prefix."posts` where `ID`='$post_id';";
                    $post_exists=$wpdb->get_results($query, ARRAY_N);
                    if (count($post_exists)>0) // post exists
                    {
                        $query="select * from `".$wpdb->prefix."spr_votes` where `post_id`='$post_id' and `user_id`='$current_user->ID';";
                        $voted=$wpdb->get_results($query, ARRAY_N);
                        if (count($voted)>0)  // already voted
                        {
                            $response=json_encode(array('status'=>2));
                        }
                        else // haven't voted yet 
                        {
                            $wpdb->query("INSERT INTO `".$wpdb->prefix."spr_votes` (`post_id`, `user_id`, `points`) VALUES ('$post_id', '$current_user->ID', '$points_');");
                            $query="select `votes`, `points` from `".$wpdb->prefix."spr_rating` where `post_id`='$post_id';";
                            $popularity=$wpdb->get_results($query, ARRAY_N);
                            $votes=$popularity[0][0];
                            $points=$popularity[0][1];
                            if ($votes==0||$points==0)
                            {
                                $wpdb->query("INSERT INTO `".$wpdb->prefix."spr_rating` (`post_id`, `votes`, `points`) VALUES ('$post_id', '1', '$points_');");
                            }
                            else
                            {
                                $points=$points+$points_;
                                $votes=$votes+1;
                                $wpdb->query("UPDATE `".$wpdb->prefix."spr_rating` set `votes`='$votes', `points`='$points' where `post_id`='$post_id';");
                            }
                            $query="select `votes`, `points` from `".$wpdb->prefix."spr_rating` where `post_id`='$post_id';";
                            $popularity=$wpdb->get_results($query, ARRAY_N);
                            $votes=$popularity[0][0];
                            $points=$popularity[0][1];
                            $html=spr_show_voted($votes, $points, $options['show_vote_count']);
                            $response=json_encode(array('status'=>1, 'html'=>$html));
                        }
                    }
                    else
                    {
                        $response=json_encode(array('status'=>3)); // post doesn't exist
                    }
                }
                else if ($options['allow_guest_vote']&&filter_var(spr_get_ip(), FILTER_VALIDATE_IP))
                {
                    $query="select * from `".$wpdb->prefix."posts` where `ID`='$post_id';";
                    $post_exists=$wpdb->get_results($query, ARRAY_N);
                    if (count($post_exists)>0) // post exists
                    {
                        $query="select * from `".$wpdb->prefix."spr_votes` where `post_id`='$post_id' and `user_id`='".spr_get_ip()."';";
                        $voted=$wpdb->get_results($query, ARRAY_N);
                        if (count($voted)>0)  // already voted
                        {
                            $response=json_encode(array('status'=>2));
                        }
                        else // haven't voted yet 
                        {
                            $wpdb->query("INSERT INTO `".$wpdb->prefix."spr_votes` (`post_id`, `user_id`, `points`) VALUES ('$post_id', '".spr_get_ip()."', '$points_');");
                            $query="select `votes`, `points` from `".$wpdb->prefix."spr_rating` where `post_id`='$post_id';";
                            $popularity=$wpdb->get_results($query, ARRAY_N);
                            $votes=$popularity[0][0];
                            $points=$popularity[0][1];
                            if ($votes==0||$points==0)
                            {
                                $wpdb->query("INSERT INTO `".$wpdb->prefix."spr_rating` (`post_id`, `votes`, `points`) VALUES ('$post_id', '1', '$points_');");
                            }
                            else
                            {
                                $points=$points+$points_;
                                $votes=$votes+1;
                                $wpdb->query("UPDATE `".$wpdb->prefix."spr_rating` set `votes`='$votes', `points`='$points' where `post_id`='$post_id';");
                            }
                            $query="select `votes`, `points` from `".$wpdb->prefix."spr_rating` where `post_id`='$post_id';";
                            $popularity=$wpdb->get_results($query, ARRAY_N);
                            $votes=$popularity[0][0];
                            $points=$popularity[0][1];
                            $html=spr_show_voted($votes, $points, $options['show_vote_count']);
                            $response=json_encode(array('status'=>1, 'html'=>$html));
                        }
                    }
                    else
                    {
                        $response=json_encode(array('status'=>3)); // post doesn't exist
                    }
                }
                else
                {
                    $response=json_encode(array('status'=>4)); // user isn't logged in
                }
            }
            else
            {
                $response=json_encode(array('status'=>5));  // key parameters aren't set
            }
        }
        else
        {
            $response=json_encode(array('status'=>6));  // key parameters aren't set
        }
    }
    else
    {
        $response=json_encode(array('status'=>7));  // rating isn't active
    }
    echo $response;
    if (isset($_POST['action']))
    {
        die();
    }
}

function spr_options()
{
    $list=spr_list_cpt_slugs();
    foreach ($list as $list_)
    {
        $def_types[$list_]=0;
    }
    $default_options=array("shape"=>"s", "color"=>"y", "where_to_show"=>$def_types, "position"=>"before", "show_vote_count"=>"1", "activated"=>"0", "scale"=>"5", "method"=>"auto", "alignment"=>"center", "vote_count_color"=>"", "vc_bold"=>"0", "vc_italic"=>"0", "show_in_loops"=>"0", "loop_on_hp"=>"0", "use_aggregated"=>"1", "allow_guest_vote"=>"0", "show_stats_metabox"=>"1");
    $options=get_option('spr_settings');
    $options=json_decode($options, true);
    $diff=array_diff_key($default_options, $options);
    if (count($diff)>0)
    {
        $options=array_merge($options, $diff);
    }
    return $options;
}

function spr_options_page()
{
    require_once (plugin_dir_path(__FILE__).'/settings.php');
}

function spr_save_settings()
{
    $current_options=spr_options();
    $current_json=json_encode($current_options);
    if (isset($_POST['spr_shape'])||isset($_POST['spr_color'])||isset($_POST['spr_position'])||isset($_POST['spr_scale'])||isset($_POST['spr_show_vote_count'])||isset($_POST['spr_activated'])||isset($_POST['spr_method'])||isset($_POST['spr_vote_count_color'])||isset($_POST['spr_vc_bold'])||isset($_POST['spr_vc_italic'])||isset($_POST['spr_show_in_loops'])||isset($_POST['spr_loop_on_hp'])||isset($_POST['spr_use_aggregated'])||isset($_POST['spr_allow_guest_vote'])||isset($_POST['spr_show_stats_metabox']))
    {

//Shape
        if (isset($_POST['spr_shape']))
        {
            switch ($_POST['spr_shape'])
            {
                case 'c' : {
                        $options['shape']='c';
                        break;
                    }
                case 'h' : {
                        $options['shape']='h';
                        break;
                    }
                case 'b' : {
                        $options['shape']='b';
                        break;
                    }
                case 's' : {
                        $options['shape']='s';
                        break;
                    }
                default: {
                        $options['shape']=$current_options['shape'];
                        break;
                    }
            }
        }
        //Color
        if (isset($_POST['spr_color']))
        {
            switch ($_POST['spr_color'])
            {
                case 'p' : {
                        $options['color']='p';
                        break;
                    }
                case 'b' : {
                        $options['color']='b';
                        break;
                    }
                case 'y' : {
                        $options['color']='y';
                        break;
                    }
                case 'r' : {
                        $options['color']='r';
                        break;
                    }
                case 'g' : {
                        $options['color']='g';
                        break;
                    }
                default: {
                        $options['color']=$current_options['color'];
                        break;
                    }
            }
        }
        //Position
        if (isset($_POST['spr_position']))
        {
            switch ($_POST['spr_position'])
            {
                case 'before' : {
                        $options['position']='before';
                        break;
                    }
                case 'after' : {
                        $options['position']='after';
                        break;
                    }
                default: {
                        $options['position']=$current_options['position'];
                        break;
                    }
            }
        }
        //Show vote count
        if (isset($_POST['spr_show_vote_count']))
        {
            $options['show_vote_count']='1';
        }
        else
        {
            $options['show_vote_count']='0';
        }
        //Activated
        if (isset($_POST['spr_activated']))
        {
            $options['activated']='1';
        }
        else
        {
            $options['activated']='0';
        }
        //Scale
        if (isset($_POST['spr_scale']))
        {
            if ($_POST['spr_scale']>=3&&$_POST['spr_scale']<=10)
            {
                $options['scale']=$_POST['spr_scale'];
            }
            else
            {
                $options['scale']=$current_options['scale'];
            }
        }
        //Method
        if (isset($_POST['spr_method']))
        {
            switch ($_POST['spr_method'])
            {
                case 'auto' : {
                        $options['method']='auto';
                        break;
                    }
                case 'manual' : {
                        $options['method']='manual';
                        break;
                    }
                default: {
                        $options['method']=$current_options['method'];
                        break;
                    }
            }
        }
        // Vote count color
        if (isset($_POST['spr_vote_count_color']))
        {
            if (preg_match('@^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$@', $_POST['spr_vote_count_color']))
            {
                $options['vote_count_color']=$_POST['spr_vote_count_color'];
            }
            else
            {
                $_POST['spr_vote_count_color']="";
            }
        }
        //Bold
        if (isset($_POST['spr_vc_bold']))
        {
            $options['vc_bold']='1';
        }
        else
        {
            $options['vc_bold']='0';
        }
        //Italic
        if (isset($_POST['spr_vc_italic']))
        {
            $options['vc_italic']='1';
        }
        else
        {
            $options['vc_italic']='0';
        }

        if (isset($_POST['spr_show_in_loops']))
        {
            $options['show_in_loops']='1';
        }
        else
        {
            $options['show_in_loops']='0';
        }
        //Loop on homepage
        if (isset($_POST['spr_loop_on_hp']))
        {
            $options['loop_on_hp']='1';
        }
        else
        {
            $options['loop_on_hp']='0';
        }
        //Use aggregated
        if (isset($_POST['spr_use_aggregated']))
        {
            $options['use_aggregated']='1';
        }
        else
        {
            $options['use_aggregated']='0';
        }

        //Allow guests to vote
        if (isset($_POST['spr_allow_guest_vote']))
        {
            $options['allow_guest_vote']='1';
        }
        else
        {
            $options['allow_guest_vote']='0';
        }
        //Show statistics metabox
        if (isset($_POST['spr_show_stats_metabox']))
        {
            $options['show_stats_metabox']='1';
        }
        else
        {
            $options['show_stats_metabox']='0';
        }

        //where to show
        $list=spr_list_cpt_slugs();
        foreach ($list as $list_)
        {
            $def_types[$list_]=0;
            if (isset($_POST[$list_]))
            {
                $options['where_to_show'][$list_]='1';
            }
            else
            {
                $options['where_to_show'][$list_]='0';
            }
        }
        $default_options=array("shape"=>"s", "color"=>"y", "where_to_show"=>$def_types, "position"=>"before", "show_vote_count"=>"1", "activated"=>"0", "scale"=>"5", "method"=>"auto", "alignment"=>"center", "vote_count_color"=>"", "vc_bold"=>"0", "vc_italic"=>"0", "show_in_loops"=>"0", "loop_on_hp"=>"0", "use_aggregated"=>"1", "allow_guest_vote"=>"0", "show_stats_metabox"=>"1");
        $diff=array_diff_key($default_options, $options);
        if (count($diff)>0)
        {
            $options=array_merge($options, $diff);
        }
        $options=json_encode($options);

        if ($current_json!=$options)
        {
            update_option('spr_settings', $options);
            echo "<div class='updated'><p>Settings were updated successfully.</p></div>";
        }
    }
}

function spr_menu()
{
    add_options_page('Simple Rating', 'Simple Rating', 'manage_options', 'spr_options', 'spr_options_page');
}

function spr_activation_func()
{
    global $wpdb;
    $query="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."spr_votes`  (
	`post_id` INT(11) NULL DEFAULT NULL,
	`user_id` TINYTEXT NULL COLLATE 'utf8_unicode_ci',
	`points` INT(11) NULL DEFAULT NULL 
)
COLLATE='utf8_unicode_ci'
ENGINE=MyISAM;
";
    $wpdb->query($query);
    $query="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."spr_rating` (
	`post_id` INT(11) NOT NULL,
	`votes` INT(11) NOT NULL,
	`points` INT(11) NOT NULL
)
COLLATE='utf8_unicode_ci'
ENGINE=MyISAM;
";
    $wpdb->query($query);
    $list=spr_list_cpt_slugs();
    foreach ($list as $list_)
    {
        $def_types[$list_]=0;
    }
    $default_options=array("shape"=>"s", "color"=>"y", "where_to_show"=>$def_types, "position"=>"before", "show_vote_count"=>"1", "activated"=>"0", "scale"=>"5", "method"=>"auto", "alignment"=>"center", "vote_count_color"=>"", "vc_bold"=>"0", "vc_italic"=>"0", "show_in_loops"=>"0", "loop_on_hp"=>"0", "use_aggregated"=>"1", "allow_guest_vote"=>"0", "show_stats_metabox"=>"1");
    add_option('spr_settings', json_encode($default_options));
    add_option('spr_version', '1.3');
}

function add_spr_checkbox()
{
    global $post;
    $type=get_post_type($post->ID);
    $disable_rating=get_post_meta($post->ID, '_spr_disable', true);
    ?>
    <div class="misc-pub-section">
        <input id="spr_disable_rating" type="checkbox" name="spr_disable_rating"  value="<?php echo $disable_rating; ?>" <?php checked($disable_rating, 1, true); ?>>
        <label for="spr_enable_rating">Disable rating for this entry</label></div>
    <?php
}

function spr_new_update_post_handler($data, $postarr)
{

    if (isset($_POST['spr_disable_rating']))
    {
        update_post_meta($postarr['ID'], '_spr_disable', '1');
    }
    else
    {
        delete_post_meta($postarr['ID'], '_spr_disable');
    }
    return $data;
}

function spr_truncate_tables()
{
    global $wpdb;
    $query="TRUNCATE TABLE `".$wpdb->prefix."spr_votes` ;";
    $wpdb->query($query);
    $query="TRUNCATE TABLE `".$wpdb->prefix."spr_rating`;";
    $wpdb->query($query);
    echo "<div class='updated'><p>All votes were cleared.</p></div>";
}

function spr_add_settings_link($links)
{

    return array_merge(
            array(
        'settings'=>'<a href="'.admin_url('options-general.php?page=spr_options').'">Settings</a>'
            ), $links
    );
}

function spr_print_additional_styles()
{
    $options=spr_options();
    $style="<style>";
    $vc_style="#spr_votes{";
    if (strlen($options['vote_count_color'])>0&&$options['show_vote_count'])
    {
        $vc_style.="color:".$options['vote_count_color']." !important;";
    }
    if ($options['vc_bold']&&$options['show_vote_count'])
    {
        $vc_style.="font-weight:700 !important;";
    }
    if ($options['vc_italic']&&$options['show_vote_count'])
    {
        $vc_style.="font-style:italic !important;";
    }
    $vc_style.="}";
    if ($vc_style!="#spr_votes{}")
    {
        $style.=$vc_style;
    }
    $style.="</style>";
    if ($style!="<style></style>")
    {
        echo $style;
    }
}

function spr_get_post_types_fo()
{
    $options=spr_options();
    $post_types=get_post_types(array('public'=>true, '_builtin'=>false), 'objects', 'and');
    $result='<table><tr><td class="spr_cb_labels">Posts</td><td><input type="checkbox" name="post" id="post" value="'.$options['where_to_show']['post'].'" '.checked($options['where_to_show']['post'], 1, false).'></td></tr><tr><td class="spr_cb_labels">Pages</td><td><input type="checkbox" name="page" id="page" value="'.$options['where_to_show']['page'].'" '.checked($options['where_to_show']['page'], 1, false).'></td></tr>';
    foreach ($post_types as $post_type)
    {
        $result.= '<tr><td class="spr_cb_labels">'.$post_type->labels->name.'</td><td><input type="checkbox" name="'.$post_type->rewrite['slug'].'" id="'.$post_type->rewrite['slug'].'" value="'.$options['where_to_show'][$post_type->rewrite['slug']].'" '.checked($options['where_to_show'][$post_type->rewrite['slug']], 1, false).'></td></tr>';
    }
    $result.="</table>";
    return $result;
}

function spr_list_cpt_slugs()
{
    $types=array("post", "page");
    $post_types=get_post_types(array('public'=>true, '_builtin'=>false), 'objects', 'and');
    foreach ($post_types as $post_type)
    {
        $types[]=$post_type->rewrite['slug'];
    }
    return $types;
}

function spr_get_ip()
{
    if (getenv("HTTP_CLIENT_IP")&&strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
    {
        $ip=getenv("HTTP_CLIENT_IP");
    }
    else if (getenv("HTTP_X_FORWARDED_FOR")&&strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
    {
        $ip=getenv("HTTP_X_FORWARDED_FOR");
    }
    else if (getenv("REMOTE_ADDR")&&strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
    {
        $ip=getenv("REMOTE_ADDR");
    }
    else if (isset($_SERVER['REMOTE_ADDR'])&&$_SERVER['REMOTE_ADDR']&&strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
    {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    else
    {
        $ip="unknown";
    }
    return($ip);
}

function spr_list_ratings($post)
{
    $options=spr_options();
    global $wpdb;
    wp_enqueue_style('spr_style', plugins_url('/resources/spr_style.css', __FILE__));
    $query="select `points`,`user_id`, count(points) as `amount` from `".$wpdb->prefix."spr_votes` where `post_id`='".$post->ID."' group by `points` order by `points` asc;";
    $list=$wpdb->get_results($query, ARRAY_A);
    $html="";
    $result="";
    if (count($list)>0)
    {
        $totalpoints=0;
        $totalvoters=0;
        for ($i=$options['scale']; $i>=1; $i--)
        {
            $users=0;
            $guests=0;
            $votes=0;
            $found=false;
            foreach ($list as $list_)
            {
                if ($list_['points']==$i)
                {
                    $found=true;
                    $totalpoints+=$list_['points']*$list_['amount'];
                    $totalvotes+=$list_['amount'];
                    $votes=$list_['amount'];
                    if (is_numeric($list_['used_id']))
                    {
                        $users++;
                    }
                    else if ($options['allow_guest_vote']&&filter_var($list_['used_id'], FILTER_VALIDATE_IP))
                    {
                        $guests++;
                    }
                }
            }
            if ($found)
            {
                if ($votes==1)
                {
                    $votesorvote='vote';
                }
                else
                {
                    $votesorvote='votes';
                }
                $html.='<div id="spr_visual_container_adm">'.spr_show_voted(1, $i, false).'<span id="spr_votes">'.$votes." ".$votesorvote."</span></div><br/>";
            }
            else
            {
                $html.='<div id="spr_visual_container_adm">'.spr_show_voted(1, $i, false)."<span id='spr_votes'>0 votes</span></div><br/>";
            }
        }
        $result='<div id="spr_visual_container_adm">'.spr_show_voted($totalvotes, $totalpoints, true)."</div><div style='text-align:center;font-size:15px;margin:3px;font-weight:700;'>Statistics by rating:</div>";
    }
    else
    {
        $html.="There are no votes for this entry yet.";
    }
    echo $result.$html;
}

function spr_add_custom_box()
{
    $options=spr_options();
    if ($options['show_stats_metabox'])
    {
        foreach ($options['where_to_show'] as $k=> $v)
        {
            if ($v)
            {
                $screens[]=$k;
            }
        }
        if (count($screens)>0)
        {
            foreach ($screens as $screen)
            {
                add_meta_box('spr_rating_stats', __('Rating statistics', 'spr_text_domain'), 'spr_list_ratings', $screen, 'side');
            }
        }
    }
}

add_action('add_meta_boxes', 'spr_add_custom_box');
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'spr_add_settings_link');
add_filter('wp_insert_post_data', 'spr_new_update_post_handler', '99', 2);
add_action('post_submitbox_misc_actions', 'add_spr_checkbox');
add_action('admin_menu', 'spr_menu');
add_action('wp_ajax_spr_rate', 'spr_rate');
add_action('wp_ajax_nopriv_spr_rate', 'spr_rate');
register_activation_hook(__FILE__, 'spr_activation_func');
?> 
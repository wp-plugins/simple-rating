<?php

/*

  Plugin Name: Simple Rating
  Description: Allows users to rate posts and pages.
  Version: 1.0
  Author: Igor Yavych
  Author URI: https://www.odesk.com/users/~~d196de64099a8aa3
 */
$options=spr_options();
if ($options['activated']==1)
{
    add_filter('the_content', 'spr_filter', 15);
}

function spr_filter($content)
{
    $options=spr_options();
    if (is_singular())
    {
        if (is_singular('post')&&($options['where_to_show']=='posts'||$options['where_to_show']=='both'))
        {
            if ($options['position']=='before')
            {
                $content=spr_rating().$content;
            }
            elseif ($options['position']=='after')
            {
                $content .= spr_rating();
            }
        }
        if (is_singular('page')&&($options['where_to_show']=='pages'||$options['where_to_show']=='both'))
        {
            if ($options['position']=='before')
            {
                $content=spr_rating().$content;
            }
            elseif ($options['position']=='after')
            {
                $content .= spr_rating()."idk";
            }
        }
    }
    return $content;
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
    if (is_user_logged_in()==1)
    {
        $query="select * from `".$wpdb->prefix."spr_votes` where `post_id`='$post->ID' and `user_id`='$current_user->ID';";
        $voted=$wpdb->get_results($query, ARRAY_N);
        if (count($voted)>0)
        {
            $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voted($votes, $points).'</div></div>';
            wp_localize_script('spr_script', 'spr_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape'], 'rating_working'=>false, 'post_id'=>$post->ID));
            return $results;
        }
        else
        {
            $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voting($votes, $points).'</div></div>';
            wp_localize_script('spr_script', 'spr_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape'], 'rating_working'=>true, 'post_id'=>$post->ID));
            return $results;
        }
    }
    else
    {
        wp_localize_script('spr_script', 'spr_ajax_object', array('ajax_url'=>admin_url('admin-ajax.php'), 'scale'=>$options['scale'], 'spr_type'=>$options['color'].$options['shape'], 'rating_working'=>false));
        $results='<div id="spr_container"><div id="spr_visual_container">'.spr_show_voted($votes, $points).'</div></div>';
        return $results;
    }
}

function spr_show_voted($votes, $points)
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
    if ($options['show_vote_count'])
    {
        if ($votes==1)
        {
            $votesorvote='vote';
        }
        else
        {
            $votesorvote='votes';
        }
        $html .= '<span class="spr_votes">'.$votes.' '.$votesorvote.'</span>';
    }
    return $html;
}

function spr_show_voting($votes, $points)
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
    if ($options['show_vote_count'])
    {
        if ($votes==1)
        {
            $votesorvote='vote';
        }
        else
        {
            $votesorvote='votes';
        }
        $html .= '<span class="spr_votes">'.$votes.' '.$votesorvote.'</span>';
    }
    return $html;
}

function spr_rate()
{
    global $current_user, $wpdb;
    $options=spr_options();
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
                        $html=spr_show_voted($votes, $points);
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
    echo $response;
    if (isset($_POST['action']))
    {
        die();
    }
}

function spr_options()
{
    $options=get_option('spr_settings');
    $options=json_decode($options, true);
    return $options;
}

function spr_options_page()
{
    require_once (plugin_dir_path(__FILE__).'/settings.php');
}

function spr_save_settings()
{
    $current_options=spr_options();
    if (isset($_POST['spr_shape'])||isset($_POST['spr_color'])||isset($_POST['spr_where_to_show'])||isset($_POST['spr_position'])||isset($_POST['spr_scale'])||isset($_POST['spr_show_vote_count'])||isset($_POST['spr_activated']))
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
        //Where to show
        if (isset($_POST['spr_where_to_show']))
        {
            switch ($_POST['spr_where_to_show'])
            {
                case 'posts' : {
                        $options['where_to_show']='posts';
                        break;
                    }
                case 'pages' : {
                        $options['where_to_show']='pages';
                        break;
                    }
                case 'both' : {
                        $options['where_to_show']='both';
                        break;
                    }
                default: {
                        $options['where_to_show']=$current_options['where_to_show'];
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


        $options=json_encode($options);
        update_option('spr_settings', $options);
    }
}

function spr_menu()
{
    add_options_page('Simple Rating', 'Simple Rating', 'manage_options', 'spr_options', 'spr_options_page');
}

function spr_activation_func()
{
    global $wpdb;
    $query="CREATE TABLE  `".$wpdb->prefix."spr_votes` (
	`post_id` INT(11) NULL DEFAULT NULL,
	`user_id` INT(11) NULL DEFAULT NULL,
	`points` INT(11) NULL DEFAULT NULL
)
COLLATE='utf8_unicode_ci'
ENGINE=MyISAM;
";
    $wpdb->query($query);
    $query="CREATE TABLE  `".$wpdb->prefix."spr_rating` (
	`post_id` INT(11) NOT NULL,
	`votes` INT(11) NOT NULL,
	`points` INT(11) NOT NULL
)
COLLATE='utf8_unicode_ci'
ENGINE=MyISAM;
";
    $wpdb->query($query);
    add_option('spr_settings', '{"shape":"y","color":"s","where_to_show":"both","position":"before","show_vote_count":"1","activated":"0","scale":"5"}');
}

add_action('admin_menu', 'spr_menu');
add_action('wp_ajax_spr_rate', 'spr_rate');
register_activation_hook(__FILE__, 'spr_activation_func');
?>
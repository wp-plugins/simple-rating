<?php

function upgrade()
{
    $version=get_current_version();
    if ($version!="1.2")
    {
        $verion=comapre_versions($version, '1.2.1');
        if ($verion=="1")
        {
            $options=spr_options();
            $list=spr_list_cpt_slugs();
            foreach ($list as $list_)
            {
                $def_types[$list_]=0;
            }
            $options['where_to_show']=$def_types;
            $options['loop_on_hp']="0";
            $options=json_encode($options);
            update_option('spr_settings', $options);
            update_option('spr_version', '1.2.1');
        }
        else if ($verion=="0")
        {
            update_option('spr_version', '1.2.1');
        }
        else if ($verion=="-1")
        {
            //No need for upgrade
        }
    }
}

function get_current_version()
{
    return get_option('spr_version', '0.0.1');
}

function comapre_versions($current, $new)
{
    $current=explode(".", $current);
    if (count($current)==2)
    {
        $current[2]=0;
    }
    $new=explode(".", $new);
    if (count($new)==2)
    {
        $new[2]=0;
    }
    if ($current[0]==$new[0])
    {
        if ($current[1]==$new[1])
        {
            if ($current[2]==$new[2])
            {
                return "-1";
            }
            else if ($current[2]<$new[2])
            {
                return "1";
            }
            else
            {
                return "0";
            }
        }
        else if ($current[1]<$new[1])
        {
            return "1";
        }
        else
        {
            return "0";
        }
    }
    else if ($current[0]<$new[0])
    {
        return "1";
    }
    else
    {
        return "0";
    }
}

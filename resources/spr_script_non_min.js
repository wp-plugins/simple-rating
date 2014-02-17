var $j = jQuery, pcl, rating_wroking, numb;
$j(".spr_rating_piece").mouseenter(function(event)
{
    rating_wroking = spr_ajax_object.rating_working;
    if (rating_wroking) {
        pcl = [];
        numb = event.target.id;
        numb = (parseInt(numb.replace('spr_piece_', '')));
        for (var i = 1; i <= spr_ajax_object.scale; i++) {
            pcl[i] = ($j("#spr_piece_" + i).attr('class')).replace('spr_rating_piece ', '');
        }
        for (var i = 1; i <= spr_ajax_object.scale; i++) {
            $j("#spr_piece_" + i).addClass('spr_' + spr_ajax_object.spr_type + '_empty');
        }
        $j(".spr_rating_piece").removeClass('spr_' + spr_ajax_object.spr_type + '_full_voting');
        $j(".spr_rating_piece").removeClass('spr_' + spr_ajax_object.spr_type + '_half_voting');
        for (i = 1; i <= numb; i++) {
            $j("#spr_piece_" + i).addClass('spr_' + spr_ajax_object.spr_type + '_full_voted');
        }
    }
}).mouseleave(function() {
    if (rating_wroking) {
        $j(".spr_rating_piece").removeClass('spr_' + spr_ajax_object.spr_type + '_full_voted');
        $j(".spr_rating_piece").removeClass('spr_' + spr_ajax_object.spr_type + '_half_voted');
        for (var i = 1; i <= spr_ajax_object.scale; i++) {
            $j("#spr_piece_" + i).addClass(pcl[i]);
        }
    }
}
);
$j(".spr_rating_piece").click(function(event) {
    rating_wroking = spr_ajax_object.rating_working;
    numb = event.target.id;
    numb = (parseInt(numb.replace('spr_piece_', '')));
    request = {'post_id': spr_ajax_object.post_id, 'points': numb, 'action': 'spr_rate'};
    if (rating_wroking) {
        if (numb >= 1 && numb <= spr_ajax_object.scale) {
            $j.ajax({
                type: "post",
                dataType: 'json',
                url: spr_ajax_object.ajax_url,
                data: request,
                success: function(answ)
                {
                    if (answ.status == 1)
                    {
                        $j('#spr_visual_container').html(answ.html);
                        rating_wroking = false;
                    }
                    else if (answ.status == 2)
                    {
                        return false;
                    }
                }
            });
        }
    }
});
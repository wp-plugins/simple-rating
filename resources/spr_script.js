var $j = jQuery, pcl, rating_working, numb;
$j( document ).ready(function() {
$j("#spr_container_" + spr_ajax_object.post_id).children("#spr_shapes").children(".spr_rating_piece").mouseenter(function(r) {
    if (rating_working = spr_ajax_object.rating_working) {
        pcl = [], numb = r.target.id, numb = parseInt(numb.replace("spr_piece_", ""));
        for (var _ = 1; _ <= spr_ajax_object.scale; _++)
            pcl[_] = $j("#spr_piece_" + _).attr("class").replace("spr_rating_piece ", "");
        for (var _ = 1; _ <= spr_ajax_object.scale; _++)
            $j("#spr_piece_" + _).addClass("spr_" + spr_ajax_object.spr_type + "_empty");
        for ($j("#spr_container_" + spr_ajax_object.post_id).children("#spr_shapes").children(".spr_rating_piece").removeClass("spr_" + spr_ajax_object.spr_type + "_full_voting"), $j("#spr_container_" + spr_ajax_object.post_id).children("#spr_shapes").children(".spr_rating_piece").removeClass("spr_" + spr_ajax_object.spr_type + "_half_voting"), _ = 1; numb >= _; _++)
            $j("#spr_piece_" + _).addClass("spr_" + spr_ajax_object.spr_type + "_full_voted")
    }
}).mouseleave(function() {
    if (rating_working) {
        $j("#spr_container_" + spr_ajax_object.post_id).children("#spr_shapes").children(".spr_rating_piece").removeClass("spr_" + spr_ajax_object.spr_type + "_full_voted"), $j("#spr_container_" + spr_ajax_object.post_id).children("#spr_shapes").children(".spr_rating_piece").removeClass("spr_" + spr_ajax_object.spr_type + "_half_voted");
        for (var r = 1; r <= spr_ajax_object.scale; r++)
            $j("#spr_piece_" + r).addClass(pcl[r])
    }
}), $j("#spr_container_" + spr_ajax_object.post_id).children("#spr_shapes").children(".spr_rating_piece").click(function(r) {
    rating_working = spr_ajax_object.rating_working, numb = r.target.id, numb = parseInt(numb.replace("spr_piece_", "")), request = {post_id: spr_ajax_object.post_id, points: numb, action: "spr_rate"}, rating_working && numb >= 1 && numb <= spr_ajax_object.scale && $j.ajax({type: "post", dataType: "json", url: spr_ajax_object.ajax_url, data: request, success: function(r) {
            if (1 == r.status)
                $j("#spr_container_" + spr_ajax_object.post_id).html(r.html), rating_working = !1;
            else if (2 == r.status)
                return!1
        }})
});
});
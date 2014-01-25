var $j = jQuery, pcl, number, type, space;
type = spr_ajax_object.spr_type;
scale = spr_ajax_object.scale;
$j(".spr_rating_piece").mouseenter(function(event)
{
    rating_wroking = spr_ajax_object.rating_working;
    pcl = [];
    numb = event.target.id;
    numb = (parseInt(numb.replace('spr_piece_', '')));
    for (var i = 1; i <= scale; i++) {
        pcl[i] = ($j("#spr_piece_" + i).attr('class')).replace('spr_rating_piece ', '');
    }
    for (var i = 1; i <= scale; i++) {
        $j("#spr_piece_" + i).addClass('spr_' + type + '_empty');
    }
    $j(".spr_rating_piece").removeClass('spr_' + type + '_full_voting');
    $j(".spr_rating_piece").removeClass('spr_' + type + '_half_voting');
    for (i = 1; i <= numb; i++) {
        $j("#spr_piece_" + i).addClass('spr_' + type + '_full_voted');
    }
}).mouseleave(function() {
    $j(".spr_rating_piece").removeClass('spr_' + type + '_full_voted');
    $j(".spr_rating_piece").removeClass('spr_' + type + '_half_voted');
    for (var i = 1; i <= scale; i++) {
        $j("#spr_piece_" + i).addClass(pcl[i]);
    }
}
);

$j("#spr_shape").change(function(event)
{
    for (var i = 1; i <= scale; i++) {
        $j("#spr_piece_" + i).removeClass('spr_' + type + '_empty');
    }
    $j(".spr_rating_piece").removeClass('spr_' + type + '_full_voting');
    $j(".spr_rating_piece").removeClass('spr_' + type + '_half_voting');
    for (i = 1; i <= scale; i++) {
        $j("#spr_piece_" + i).removeClass('spr_' + type + '_full_voting');
    }
    type = $j("#spr_color option:selected").val() + $j("#spr_shape option:selected").val();
    for (i = 1; i <= scale; i++) {
        $j("#spr_piece_" + i).addClass('spr_' + type + '_full_voting');
    }
})

$j("#spr_color").change(function(event)
{
    for (var i = 1; i <= scale; i++) {
        $j("#spr_piece_" + i).removeClass('spr_' + type + '_empty');
    }
    $j(".spr_rating_piece").removeClass('spr_' + type + '_full_voting');
    $j(".spr_rating_piece").removeClass('spr_' + type + '_half_voting');
    for (i = 1; i <= scale; i++) {
        $j("#spr_piece_" + i).removeClass('spr_' + type + '_full_voting');
    }
    type = $j("#spr_color option:selected").val() + $j("#spr_shape option:selected").val();
    for (i = 1; i <= scale; i++) {
        $j("#spr_piece_" + i).addClass('spr_' + type + '_full_voting');
    }
})

$j("#spr_scale").on('input', function(event)
{
    scale = $j("#spr_scale").val();
    if (scale >= 3 && scale <= 10) {

        $j("#spr_shapes").html('');
        for (i = 1; i <= scale; i++) {
            $j("#spr_shapes").html($j("#spr_shapes").html() + '<span id="spr_piece_' + i + '" class="spr_rating_piece spr_' + type + '_full_voting"></span>');
            $j("#spr_piece_" + i).addClass();
        }
    }
    else {
        $j("#spr_shapes").html('');
        for (i = 1; i <= 5; i++) {
            $j("#spr_shapes").html($j("#spr_shapes").html() + '<span id="spr_piece_' + i + '" class="spr_rating_piece spr_' + type + '_full_voting"></span>');
            $j("#spr_piece_" + i).addClass();
        }
    }
})

$j("#spr_show_vote_count").change(function(event)
{
    if ($j(this).is(":checked")) {
        $j(".spr_votes").html('5 votes');
    }
    else
        $j(".spr_votes").html('');
})
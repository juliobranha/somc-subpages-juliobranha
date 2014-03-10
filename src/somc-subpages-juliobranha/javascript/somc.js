jQuery(document).ready(function($) {
    
    // expand-collapse levels

    $( ".pages-tree li .parent" ).click(function(e) {
        e.preventDefault();
        $(this).parent().children('ul').slideToggle( "slow" );
        $(this).toggleClass('collapsed'),
        $(this).toggleClass('expanded');     
    });
    
    
    //order alphabetically
    
    $('.pages-tree li .alpha_order').click(function(e) {
        e.preventDefault();
        var $sort = this;
        var $list = $(this).parent().children("ul");
        var $listLi = $('>li',$list);
        $listLi.sort(function(a, b){
            var keyA = $.trim($(a).text());
            var keyB = $.trim($(b).text());
            if($($sort).hasClass('desc')){
                return (keyA > keyB) ? 1 : 0;
            } else {
                return (keyA < keyB) ? 1 : 0;
            }
        });
        $.each($listLi, function(index, row){
            $list.append(row);
        });
        $(this).toggleClass('asc'),
        $(this).toggleClass('desc');
    });
});





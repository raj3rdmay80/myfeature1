require([
    'jquery',
    'domReady!'
    ], function($){

    $(document).on('click', '.view-plan-details',function(){
        
        // Replace the text when it's opened
        $(this).text(function(){
            if ($(this).text() == 'View Details'){
                return 'Hide Details';
            }else{
                return 'View Details';
            }
        });
        $(this).siblings('.detail-options').toggle();
    });

     $(document).on('click','.list-open',function(){
        $(this).find('i.fa-angle-down').css({'transform': 'rotate(-180deg)'});
        if($(this).next().hasClass('show')){
            $(this).next().removeClass('show');
            $(this).next().slideUp();
            $(this).find('i.fa-angle-down').css({'transform': ''});
        }else{
            $(this).parent().parent().find('li .list-details').removeClass('show');
            $(this).parent().parent().find('li .list-details').slideUp();
            $(this).next().toggleClass('show');
            $(this).next().slideToggle();
        }
    });
});
export const addAnimationToIconsHover = () => {
    let linkWithIcons = $('.tabs');

    linkWithIcons.each(function(i, link) {
        $(link).hover(function() {

            $(this).find('.fa').addClass('fa-pulse');

        }, function() {
            $(this).find('.fa').removeClass('fa-pulse');
        });
    });
}
$(document).ready(function () {
    $('.vote').on('click', function () {
        vote($(this));
    });
});

/**
 *
 * @param elem
 */
function vote(elem) {
    let id_post = elem.closest('.post').data('id');
    $.post('./ajax/vote', {'id_post': id_post}, function(data) {

    }, 'json');
}
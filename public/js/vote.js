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
    let post = elem.closest('.post');
    let id_post = post.data('id');
    let nbVote = post.find('.vote_number').text();
    $.post('./ajax/vote', {'id_post': id_post}, function(data) {
        post.find('.vote_number').text(Number(nbVote) +1);
        iziToast.success({
            title: 'OK',
            message: data.message,
        });
    }, 'json');
}
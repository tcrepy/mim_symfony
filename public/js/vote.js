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
    $.post('/ajax/vote/'+id_post, {'id_post': id_post}, function(data) {
        if (data.etat === 'conf') {
            post.find('.vote_number').text(Number(nbVote) +1);
            post.find('.nombreVotes').text(Number(nbVote)+1 + ' vote(s)');
            iziToast.success({
                title: 'OK',
                message: data.message,
            });
        } else {
            iziToast.error({
                title: 'Erreur',
                message:data.message,
            });
        }
    }, 'json');
}
let voteQuestion = {
    init: function()
    {
        $("#upvote").click(function(evt){
            voteQuestion.votingAction = 'upvote';
            evt.preventDefault();
            voteQuestion.voteAction($(this));

        });
        $("#downvote").click(function(evt){
            voteQuestion.votingAction = 'downvote';
            evt.preventDefault();
            voteQuestion.voteAction($(this));

        });
    },
    voteAction: function($element)
    {

        let idQuestion = $element.data('id');

        $.ajax({
            url: voteQuestionRoute,
            method: 'POST',
            data: {
                id : idQuestion,
                action: voteQuestion.votingAction

            },
            dataType: 'json',
            success: function(xhr)
            {
                let message = JSON.parse(xhr);
                console.log(message.voteCount);

                $('#voteCount').text(message.voteCount);



            },
            error: function(err)
            {

            }
        });
    }

};
$(voteQuestion.init);
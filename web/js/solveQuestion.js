let solveQuestion = {
    init: function()
    {
        $("#solveQuestion").click(solveQuestion.solveQuestionAction);
    },
    solveQuestionAction: function(evt)
    {
        evt.preventDefault();
        let idQuestion = $(this).data('id');

        $.ajax({
            url: solveQuestionRoute,
            method: 'POST',
            data: {
                id : idQuestion
            },
            success: function(xhr, data)
            {

                if(data === "success")
                {
                    $("#question-status").removeClass('is-warning').addClass('is-danger').text('Résolu');
                    $("#solveQuestion").remove();
                }
            },
            error: function(err)
            {

            }
        });
    }

};
$(solveQuestion.init);
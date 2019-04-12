let hideQuestion = {
    init: function()
    {
        $(".hideAnswer").click(hideQuestion.hideAnswerAction);
        $(".hideQuestion").click(hideQuestion.hideQuestionAction);
    },
    hideAnswerAction: function(evt)
    {
        evt.preventDefault();
        let idAnswer = $(this).data('id');
        let $element = $(this);

        $.ajax({
            url: hideQuestionRoute,
            method: 'POST',
            data: {
                id : idAnswer
            },
            success: function(xhr, data)
            {
                if(data === "success")
                {
                    $element.parent().fadeOut(200);
                }
            },
            error: function(err)
            {

            }
        });
    },
    hideQuestionAction: function(evt)
    {
        evt.preventDefault();
        let idQuestion = $(this).data('id');
        let $element = $(this);

        $.ajax({
            url: 'http://localhost/evalsymfo/Faq/web/app_dev.php/question/hide',
            method: 'POST',
            data: {
                id : idQuestion
            },
            success: function(xhr, data)
            {
                if(data === "success")
                {
                    $element.parent().addClass('background-grey');
                }
            },
            error: function(err)
            {

            }
        });
    }

};
$(hideQuestion.init);
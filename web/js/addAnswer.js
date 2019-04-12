let addAnswer = {
    init: function()
    {
        $("#add-answer").submit(addAnswer.addAnswerAction);
    },
    addAnswerAction: function(evt)
    {
        evt.preventDefault();
        let idQuestion = $(this).data('id');

        var token = $('#appbundle_answer__token').val();
        var content = $('#appbundle_answer_content').val();

        $.ajax({
            url: addAnswerRoute,
            method: 'POST',
            data: {
                'appbundle_answer[content]' : content,
                'appbundle_answer[_token]': token

            },
            success: function(xhr, data)
            {
                if(xhr)
                {
                    let answer = JSON.parse(xhr);

                   let html = `<article class="media">

                    <figure class="media-left">
                    <p class="image is-64x64">
                    <img src="https://bulma.io/images/placeholders/128x128.png">
                    </p>
                    </figure>
                    <div class="media-content">
                    <div class="content">
                    <p>
                    <strong>${answer.user}</strong>
                    <br>
                    ${answer.content}
                    <br>
                    <small>Le ${answer.date}</small>
    
                    </p>
                    </div>
                    </div>`;
                    if($('#answer-list').length === 0 )
                    {
                        $('<div>').addClass('box').attr('id', 'answer-list').insertAfter('#question-show');
                    }
                   $('#answer-list').append(html);


                }
            },
            error: function(err)
            {

            }
        });

    }

};
$(addAnswer.init);
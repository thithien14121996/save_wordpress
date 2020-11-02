$('document').ready(()=>{
    $('#shrief-save-button').hide();
    $('#shrief-new-button').click(function (e) { 
        e.preventDefault();
        const note_title = $('#shrief-note-title').val();
        const note_content = $('#shrief-note-content').val();
        $.post(ajaxurl, {
            'action' : 'shrief_insert_post',
            'note_title' :  note_title,
            'note_content' :  note_content,
        },
            function (response) {
                var data = JSON.parse(response);
                var htmlResponse = '<div class="list-item ui-state-default" id="shrief-item-' + data.post_id +  '">' 
                + '<input name="shrief_note_title" id="" type="text" placeholder="Note title" value="' + data.post_title + '" readonly></input>'
                + '<input type="submit" name="shrief_edit_button" id="" class="shrief-button shrief-edit-button shrief-button-'+ data.post_id + '" value="Edit">'
                + '<input type="submit" name="shrief_delete_button" class="shrief-button shrief-delete-button shrief-button-'+ data.post_id + '" value="Delete">'
                + '</div>'
                $('#sortable').prepend(htmlResponse);
            }
        );

    });
    $('#shrief-save-button').click(function (e) { 
        e.preventDefault();
        const note_title = $('#shrief-note-title').val();
        const note_content = $('#shrief-note-content').val();
        var post_id = e.target.classList[2];
        post_id = parseInt(post_id.replace('shrief-save-button-',''));
        console.log(post_id)
        $.post(ajaxurl, {
            'action' : 'shrief_save_post',
            'note_title' :  note_title,
            'note_content' :  note_content,
            'post_id' : post_id
        },
            function (response) {
                var data = JSON.parse(response);
                var htmlResponse = 
                '<input name="shrief_note_title" id="" type="text" placeholder="Note title" value="' + data.post_title + '" readonly></input>'
                + '<input type="submit" name="shrief_edit_button" id="" class="shrief-button shrief-edit-button shrief-button-'+ data.post_id + '" value="Edit">'
                + '<input type="submit" name="shrief_delete_button" class="shrief-button shrief-delete-button shrief-button-'+ data.post_id + '" value="Delete">'
                $('#shrief-item-'+data.post_id).html(htmlResponse);
                $('#shrief-save-button').hide();
                $('#shrief-note-title').val('');
                $('#shrief-note-content').val('');

            }
        );
    });
    $('.shrief-delete-button').on('click',function (e) { 
        e.preventDefault();
        console.log('here');
        var post_id = e.target.classList[2];
        post_id = parseInt(post_id.replace('shrief-button-',''));
        
        $.post(ajaxurl, {
            'action' : 'shrief_delete_post',
            'post_id' : post_id
        },
            function (response) {
                $('#shrief-item-'+post_id).remove();
            }
        );
    });

    function sortable_change(data){
        $.post(ajaxurl,{
            'action' : 'shrief_sort_change',
            'data' : data
        },
        function(response){
            console.log(response);
        })
    }
    function get_number(string){
        return string.replace('shrief-item-','');
    }
    $('#sortable').sortable({
        stop: function( event, ui ) {
            var array_item = $('#sortable').sortable('toArray');
            array_item = array_item.map(item=> parseInt(get_number(item)) );
            sortable_change(array_item)
        }
    });

    $('.shrief-edit-button').click((e)=>{
        e.preventDefault();
        var post_id = e.target.classList[2];
        $('#shrief-save-button').show();
        post_id = parseInt(post_id.replace('shrief-button-',''));
        $.post(ajaxurl,{
            'action' : 'shrief_edit',
            'data' : post_id
        },
        function(response){
            res = JSON.parse(response);
            console.log(res);
            $('#shrief-note-title').val(res.title)
            $('#shrief-save-button').addClass('shrief-save-button-'+ res.post_id);
            $('#shrief-note-content').text(res.content)
        })
    })

    
})


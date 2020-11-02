<div class="wrap">
    <form action="" method="POST" class="shrief-note-form">
        <div id="sortable" class="list-note">
            <?php 
                $posts = ShriefPlugin::get_posts_with_meta_query();
                foreach ($posts as $post) {
                    $position = get_post_meta($post->ID,'_note_position',true);
                    ?>
                    <div class="list-item ui-state-default" id="shrief-item-<?php echo $post->ID ?>">
                        <input name="shrief_note_title" id="" type="text" placeholder="Note title" value="<?php echo $post->post_title ?>" readonly></input>
                        <input type="submit" name="shrief_edit_button" id="" class="shrief-button shrief-edit-button shrief-button-<?php echo $post->ID ?>" value="Edit">
                        <input type="submit" name="shrief_delete_button" class="shrief-button shrief-delete-button shrief-button-<?php echo $post->ID ?>" value="Delete">
                    </div>
            <?php        
                }
            ?>
        </div>
        <div class="note-control">
            <div class="note-control-container">
                <div class="note-title">
                    <input name="shrief_note_title" id="shrief-note-title" type="text" value="" placeholder="Note title"></input>
                </div>
                <div class="note-content">
                    <textarea name="shrief_note_content" rows="10" id="shrief-note-content" type="text" placeholder="Note Content"></textarea>
                </div>
                <div class="note-control-button-group">
                    <input type="submit" class="shrief-button shrief-save-button" id="shrief-save-button" name="shrief_save_button" value="Save">
                    <input type="submit" class="shrief-button shrief-new-button" id="shrief-new-button" name="shrief_new_button" value="New Note">
                </div>
                 </div>
        </div>
    </form>
    
</div>
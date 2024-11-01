<div class="wrap">
    
    <h2>Synced.io - Edit Category</h2>
    
    <form method="POST">
        
            
        <table class="form-table">
            
            <?php if (isset($z->data)) { ?>
                    <tr>
                        <td style="width:20%"><label>Synced Category</label></td>
                        <td><input readonly type="text" name="category_name" value="<?=$z->data->category_name?>" style="width:90%"></td>
                    </tr>
                    <tr>
                        <td style="width:20%"><label>WP Category</label></td>
                        <td>
                            <select name="wp_term_id">
                                <option value="-">-</option>
                                <?php foreach($z->terms as $term) { ?>
                                <option <?=($term->term_id == $z->data->wp_id) ? 'selected' : ''?> value="<?=$term->term_id?>"><?=$term->name?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><input type="submit" class="button button-primary" value="Assign" name="assign"></td>
                    </tr>
            <?php } ?>        

        </table>
    </form>
</div>    

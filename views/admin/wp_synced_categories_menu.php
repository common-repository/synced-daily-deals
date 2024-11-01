<div class="wrap">
    
    <h2>Synced.io - Categories</h2>
    <p></p>
    
    <form method="POST">
        
            
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th style="width:100px">SEL</th>
                    <th style="width:100px">Category ID</th>
                    <th style="width:350px">Category Name</th>
                    <th>WP Category</th>
                    <th>Assign</th>
                </tr>
            </thead>
            <?php foreach($z->data as $row) { ?>
                    <tr>
                        <td><input type="checkbox" name="x"></td>
                        <td><?= $row['category_id']?></td>
                        <td><b><?= $row['category_name']?></b></td>
                        <td><?= ($row['wp_id'] > 0) ? $row['term_name'] : ''?></td>
                        <td><a class="button button-primary" href="<?=menu_page_url( 'wp_synced_categories', 0 ) . '&edit=1&category_id=' . $row['category_id']?>">Assign</a></td>
                    </tr>
                    <?php foreach($row['subcategories'] as $srow) { ?>
                            <tr>
                                <td><input type="checkbox" name="x"></td>
                                <td><?= $srow['category_id']?></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $srow['category_name']?></td>
                                <td><?= ($srow['wp_id'] > 0) ? $srow['term_name'] : ''?></td>
                                <td><a class="button button-primary" href="<?=menu_page_url( 'wp_synced_categories', 0 ) . '&edit=1&category_id=' . $srow['category_id']?>">Assign</a></td>
                            </tr>
                    <?php } ?>                    
            <?php } ?>

            <?php if (count($z->data) == 0) { ?>
                    <tr><td colspan="6">There is no data . Please run Import Merchants !</td></tr>
            <?php } ?>

        </table>
    </form>
</div>    

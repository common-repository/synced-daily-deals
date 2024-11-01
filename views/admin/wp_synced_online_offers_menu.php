<div class="wrap">

    <h2>Synced.io - Online Offers</h2>
    <p></p>

    <form method="POST">
    
        
        <div class="tablenav top">

            <div class="alignleft actions">
                
                <input type="text" value="<?= $z->input_search['merchant'] ?>" name="input_search_merchant" placeholder="Enter Merchant">
                <select name="input_search_published">
                    <option value="all">All</option>
                    <option <?=($z->input_search['published'] == 'published') ? 'selected' : ''?> value="published">Published</option>
                    <option <?=($z->input_search['published'] == 'not_published') ? 'selected' : ''?> value="not_published">Not Published</option>
                </select>
                <input type="submit" value="Search Offers" class="button" name="action_search">
                <input name="import_offers" type="submit" class="button button-primary" value="Import Data" onclick="if (confirm('Are you sure you want to import the data ?')) return true; else return false;">
                <input name="build_posts" type="submit" class="button button-primary" value="Publish Offers">
                <input name="delete_posts" type="submit" class="button button-primary" value="UnPublish Offers">                
            </div>            

            <div class="tablenav-pages">
                <span class="displaying-num"><?= $z->total ?> items</span>
                <span class="pagination-links"><?= paginate_links($z->args); ?></span>
            </div>
            
        </div>        
        
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th style="width:50px">SEL</th>
                    <th style="width:100px">Offer ID</th>
                    <th style="width:200px">Merchant</th>                    
                    <th style="width:200px">Offer Title</th>
                    <th style="width:200px">Offer Category</th>                    
                    <th>Offer Description</th>
                    <th>Affiliate Url</th>
                    <th style="width:200px">Voucher Code</th>
                    <th style="width:200px">WP Page</th>
                </tr>
            </thead>
            <?php
            foreach ($z->data as $row)
            {
                ?>
                <tr>
                    <td><input type="checkbox" name="sel[]" value="<?= $row['offer_id'] ?>"></td>
                    <td><?= $row['offer_id'] ?></td>
                    <td><?= $row['merchant_name'] ?></td>                        
                    <td><?= $row['offer_title'] ?></td>
                    <td><?= $row['category_name'] ?></td>
                    <td><?= $row['offer_description'] ?></td>
                    <td><?= urldecode($row['offer_affiliate_url']) ?></td>
                    <td><?= $row['offer_voucher_code'] ?></td>
                    <td><?= ($row['wp_id'] > 0) ? $row['wp_post_name'] : '' ?></td>
                </tr>
            <?php } ?>

            <?php
            if (count($z->data) == 0)
            {
                ?>
                <tr><td colspan="6">There is no data . Please run Import Offers !</td></tr>
<?php } ?>

        </table>

        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="displaying-num"><?= $z->total ?> items</span>
                <span class="pagination-links"><?= paginate_links($z->args); ?></span>
            </div>
        </div>

    </form>



</div>    

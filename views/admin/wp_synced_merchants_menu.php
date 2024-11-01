<div class="wrap">

    <h2>Synced.io - Merchants</h2>

    <p></p>
    <form method="POST">

        

        <div class="tablenav top">

            <div class="alignleft actions">
                <label class="screen-reader-text" for="filter-by-date">Filter by date</label>
                <input type="text" value="<?= $z->input_search ?>" name="input_search">
                <input type="submit" value="Search Merchants" class="button" name="action_search">
                <input name="import_merchants" type="submit" class="button button-primary" value="Import Data" onclick="if (confirm('Are you sure you want to import the data ?')) return true; else return false;">
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
                    <th style="width:100px">Merchant ID</th>
                    <th style="width:200px">Merchant Name</th>
                    <th style="width:100px">Online Offers</th>                     
                    <th style="width:200px">Category Name</th>                    
                    <th>Merchant Description</th>
                    <th>Merchant Url</th>
                    <th style="width:200px">Merchant Logo</th>   

                </tr>
            </thead>
            <?php
            $k = 0;
            foreach ($z->data as $row)
            {
                ?>
                <tr class="<?= ($k++ % 2 == 0) ? 'alternate' : '' ?>">
                    <td><input type="checkbox" name="sel[]" value="<?= $row['merchant_id'] ?>"></td>
                    <td><?= $row['merchant_id'] ?></td>
                    <td><?= $row['merchant_name'] ?></td>
                    <td><?= $row['count_offers'] ?></td>                    
                    <td><?= $row['category_name'] ?></td>                        
                    <td><?= $row['merchant_description'] ?></td>
                    <td><?= $row['merchant_url'] ?></td>
                    <td><?= ($row['merchant_logo'] <> '') ? '<img src="' . $row['merchant_logo'] . '" alt="' . $row['merchant_name'] . '">' : '' ?></td>

                </tr>
            <?php } ?>

            <?php if (count($z->data) == 0)
            {
                ?>
                <tr><td colspan="7">There is no data . Please run Import Merchants !</td></tr>
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

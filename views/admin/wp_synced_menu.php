<div class="wrap">

    <h2>Synced.io - Settings</h2>
    <p></p>
    
    <form method="POST" action="">

        <table class="form-table">

            <tbody>
                <tr>
                    <th><label>API End Point</label></th>
                    <td><input style="width:350px" type="text" name="api_wsdl" value="<?= (isset($z->settings['api_wsdl'])) ? $z->settings['api_wsdl'] : '' ?>"></td>                
                </tr>
                <tr>
                    <th><label>API Username</label></th>
                    <td><input style="width:350px" type="text" name="api_username" value="<?= (isset($z->settings['api_username'])) ? $z->settings['api_username'] : '' ?>"></td>
                </tr>            
                <tr>
                    <th><label>API Subscription ID</label></th>
                    <td><input style="width:350px" type="text" name="api_password" value="<?= (isset($z->settings['api_password'])) ? $z->settings['api_password'] : '' ?>"></td>
                </tr>  
                <tr>
                    <th><label>API Countries</label></th>
                    <td>
                        <select style="min-width:350px" name="api_countries">
                            <?php foreach ($z->countries as $country)
                            { ?>
                                <option <?= (isset($z->settings['api_countries']) && ($z->settings['api_countries'] == $country['country_id'])) ? 'selected' : '' ?> value="<?= $country['country_id'] ?>"><?= $country['country_name'] ?></option>
<?php } ?>
                        </select>    
                    </td>                
                </tr>    

                <tr style="display:none">
                    <td><label>Schedule task</label></td>
                    <td>
                        <select style="width:350px" name="api_schedule">
                            <option <?= (isset($z->settings['api_schedule']) && $z->settings['api_schedule'] == 'hourly') ? 'selected' : '' ?> value="hourly">Hourly</option>
                            <option <?= (isset($z->settings['api_schedule']) && $z->settings['api_schedule'] == 'twicedaily') ? 'selected' : '' ?> value="twicedaily">Twicedaily</option>
                            <option <?= (isset($z->settings['api_schedule']) && $z->settings['api_schedule'] == 'daily') ? 'selected' : '' ?> value="daily">Daily</option>
                        </select>    
                    </td>                
                </tr> 

                <tr>
                    <th><label>Offer Type</label></th>
                    <td>
                        <select style="width:350px" name="api_offer_type">
                            <option <?= (isset($z->settings['offer_type']) && $z->settings['offer_type'] == 'daily_deal') ? 'selected' : '' ?> value="daily_deal">Daily Deal</option>
                        </select>    
                    </td>                
                </tr> 
            </tbody>
        </table>

        <hr>
        <h3>What happen at import with published offers ?</h3>
            
        <table class="form-table">

            <tbody>
                <tr>
                    <th><label>Merchant associated with the published offer become unjoined or rejected</label></th>
                    <td>
                        <select style="width:350px" name="merchant_unjoined">
                            <option <?= (isset($z->settings['merchant_unjoined']) && $z->settings['merchant_unjoined'] == 'nothing') ? 'selected' : '' ?> value="nothing">Do nothing</option>
                            <option <?= (isset($z->settings['merchant_unjoined']) && $z->settings['merchant_unjoined'] == 'delete') ? 'selected' : '' ?> value="delete">Delete all the published offers for this merchant</option>
                        </select>
                    </td>                
                </tr>
                <tr>
                    <th><label>Published offer is not available anymore in the feed</label></th>
                    <td>
                        <select style="width:350px" name="offer_unjoined">
                            <option <?= (isset($z->settings['offer_unjoined']) && $z->settings['offer_unjoined'] == 'nothing') ? 'selected' : '' ?> value="nothing">Do nothing</option>
                            <option <?= (isset($z->settings['offer_unjoined']) && $z->settings['offer_unjoined'] == 'delete') ? 'selected' : '' ?> value="delete">Delete the published offer</option>
                        </select>
                    </td>                
                </tr>                
            </tbody>
            
        </table>    

        <input name="save" type="submit" class="button button-primary" value="Save Configuration">

    </form>

</div>


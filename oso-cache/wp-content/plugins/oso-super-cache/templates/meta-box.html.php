<div id="OSOSuperCache">
    <div class="meta-box">
        <?php
        if (empty($data['cachedPageData'][0])) {
        ?>
        <p><?php echo $data['notCachedMessage']; ?></p>
        <?php
        } else {
        ?>
        <div class="form-group">
            <input<?php echo !empty($data['cachedPageData'][0]->dont_cache) ? ' checked' : '' ;?> id="oso-super-cache-dont-cache" type="checkbox" name="oso-super-cache[dont_cache]" value="1">
            <label for="oso-super-cache-dont-cache"><?php _ex('Exclude this page from the cache', 'Meta box setting', 'oso-super-cache'); ?></label>
        </div>

        <hr>

        <div class="form-group">
            <span class="headline"><?php  _ex('Cached page from:', 'Meta box title', 'oso-super-cache'); ?></span>
            <span class="info"><?php echo $data['lastUpdated'] != '0000-00-00 00:00:00' ? $data['lastUpdated'] : $data['refreshCacheMessage']; ?></span>
        </div>

        <div class="form-group">
            <input id="oso-super-cache-refresh-cache" type="checkbox" name="oso-super-cache[refresh_cache]" value="1">
            <label for="oso-super-cache-refresh-cache"><?php _ex('Refresh cache immediately', 'Meta box setting', 'oso-super-cache'); ?></label>
        </div>

        <input type="hidden" name="oso-super-cache[formSend]" value="1">
        <?php
        }
        ?>
        <input type="hidden" name="oso-super-cache[metaBox]" value="1">
    </div>
</div>
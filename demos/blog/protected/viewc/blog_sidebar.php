        <h2>Tags :</h2>
        <?php if (!Doo::cache('front')->getPart('sidebarTag', 300)): ?>
<?php Doo::cache('front')->start('sidebarTag'); ?>
        <?php foreach($data['randomTags'] as $k1=>$v1): ?>
        <span class="tag"><a href="<?php echo $data['rootUrl']; ?>tag/<?php echo $v1->name; ?>"><?php echo $v1->name; ?></a></span>
        <?php endforeach; ?>
        
<?php Doo::cache('front')->end(); ?>
<?php endif; ?>

        <h2>Archives</h2>
        <ul>
          <?php if (!Doo::cache('front')->getPart('sidebarArchive', 31536000)): ?>
<?php Doo::cache('front')->start('sidebarArchive'); ?>
          <?php foreach($data['archives'] as $k1=>$v1): ?>
              <?php foreach($v1 as $k2=>$v2): ?>
                <li>
                    <!-- Or you can use longer form archives' And key  archives' value' key -->
                    <a href="<?php echo $data['rootUrl']; ?>archive/<?php echo $k1; ?>/<?php echo $k2; ?>">
                        <span><?php echo month($k2); ?></span> <?php echo $k1; ?>
                    </a>
                    (<?php echo $v2; ?>)
                </li>
              <?php endforeach; ?>
          <?php endforeach; ?>
          
<?php Doo::cache('front')->end(); ?>
<?php endif; ?>
        </ul>

        <br/>
        <script type="text/javascript"><!--
        google_ad_client = "pub-0119468645083312";
        google_ad_slot = "8043445324";
        google_ad_width = 120;
        google_ad_height = 600;
        //-->
        </script>
        <script type="text/javascript"
        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
        </script>
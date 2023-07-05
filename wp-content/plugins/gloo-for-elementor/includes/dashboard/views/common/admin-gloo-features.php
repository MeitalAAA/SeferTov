<?php $images_2 = array_slice($interactor_images, 0, 4); ?> 
<div class="gloo-interactor">
    <div class="gloo-tutorials">
        <?php if(!empty($images_2)): 
            foreach($images_2 as $file): ?>
                <div class="gloo-image">
                    <a href="<?php echo $file['link']; ?>" target="_blank">
                        <img src="<?php echo $file['img_url']; ?>"/>
                    </a>
                </div>
            <?php endforeach; 
        endif; ?>
    </div>
    
    <div class="gloo-links">
        <a href="https://gloo.ooo"><?php _e( 'Visit Gloo.ooo' ); ?></a>
    </div>

    <ol>
        <li><a href="https://www.facebook.com/Gloo4elementor" target="_blank"><img src="<?php echo $images_url . '003-facebook-circular-logo.png' ?>"/></a>
        </li>
        <li><a href="https://www.youtube.com/channel/UCuKMAoeimhNpuvSDvR_9oHQ" target="_blank"><img src="<?php echo $images_url . '002-youtube.png' ?>"/></a></li>
    </ol>
</div>
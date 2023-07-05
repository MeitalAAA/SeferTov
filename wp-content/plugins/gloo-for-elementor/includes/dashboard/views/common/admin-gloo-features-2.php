<?php $images_1 = array_slice($interactor_images, 4, 7); ?>
<div class="gloo-interactor">
    <div class="gloo-tutorials">
        <?php if(!empty($images_1)): 
            foreach($images_1 as $file): ?>
                <div class="gloo-image">
                    <a href="<?php echo $file['link']; ?>" target="_blank">
                        <img src="<?php echo $file['img_url']; ?>"/>
                    </a>
                </div>
            <?php endforeach; 
        endif; ?>
    </div>
</div>
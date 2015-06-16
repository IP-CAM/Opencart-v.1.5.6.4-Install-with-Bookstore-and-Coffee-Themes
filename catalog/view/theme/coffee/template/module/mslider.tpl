<div id="mslider" class="mslider" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;
     background: url(catalog/view/javascript/jquery/mslider/gfx/<?= $shadowtype ?>.png) no-repeat bottom center;">
                    <div class="msliderContent">
                    <?php foreach ($banners as $banner) { ?>
                        <div class="item"><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" /></a></div>
						<?php } ?>
                    </div>
   
</div>
<div style="height:10px; clear:both;"></div> 
<script type="text/javascript">
    $(function() {
        $('#mslider').mobilyslider({
        transition: '<?php echo $animtype; ?>',
        animationSpeed: 500,
        autoplay: true,
        autoplaySpeed: 3000,
        pauseOnHover: true,
        bullets: false
    });
});
</script>
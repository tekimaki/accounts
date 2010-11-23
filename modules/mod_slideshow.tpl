<div class="section-col1">
	{if $gContent->mInfo.slideshow_images_slideshow}
		{slideshow imagesHash=$gContent->mInfo.slideshow_images_slideshow rel=slideshow_images}
		<div>
			{$gContent->mInfo.slideshow_images_slideshow[0].image_caption}
		</div>
	{/if}
</div>
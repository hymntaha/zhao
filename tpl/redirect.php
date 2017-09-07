<?php  require_once 'header.php'; ?>


<div class="outer-container">
	<div class="container">

		<div class="content-main">

			<div class="redirect">
				<?php if ($fbId != NULL && $storySlug !=NULL):?>
					<div class="redirect-text">Your story has been approved. Tell them on Facebook!</div>
					<div class = "message-header">Message:</div>
					<textarea class="message-textarea" id="story-message">Hi there,

I wrote a story about you that's been published on BravoYourCity.com! If you like it, please share.

<?=$storyMessage?></textarea>
					<div class="facebook-message-help" >

                                          Facebook won't let us send this message directly, but if you click on the
                                          button below, it'll copy the message to the clipboard and take you directly
                                          to Facebook messages. There you can paste it in (control-V or command-V on your keyboard)
                                          and let your subject know they've been featured!

                                        </div>
					<div class="redirect-button" id="redirect-button">Copy and go to Facebook</div>
	  			<div id="fb-link" class="hidden" data-link="<?=$fbMessageLink?>"></div>
  			<?php else:?>
  				<div class="redirect-text">It seems like there was an issue getting the proper redirect!</div>
				<?php endif?>
  			
			</div>

		</div>

	</div>
</div>

<?php  require_once 'footer.php'; ?>


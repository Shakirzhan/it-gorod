<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arParams)):?>
	<?$this->setFrameMode(true);?>
	<div class="social-buttons">
		<?if(!empty($arParams["LINK_VK"])):?>
			<a class="social-icon" href="<?=$arParams['LINK_VK']?>" target="_blank" rel="nofollow" title="Vkontakte">
				<i class="fa fa-vk" aria-hidden="true"></i>
			</a>
		<?endif;?>
		<?if(!empty($arParams["LINK_FB"])):?>
			<a class="social-icon" href="<?=$arParams['LINK_FB']?>" target="_blank" rel="nofollow" title="Facebook">
				<i class="fa fa-facebook" aria-hidden="true"></i>
			</a>
		<?endif;?>
		<?if(!empty($arParams["LINK_TWITTER"])):?>
			<a class="social-icon" href="<?=$arParams['LINK_TWITTER']?>" target="_blank" rel="nofollow" title="Twitter">
				<i class="fa fa-twitter" aria-hidden="true"></i>
			</a>
		<?endif;?>
		<?if(!empty($arParams["LINK_INSTAGRAM"])):?>
			<a class="social-icon" href="<?=$arParams['LINK_INSTAGRAM']?>" target="_blank" rel="nofollow" title="Instagram">
				<i class="fa fa-instagram" aria-hidden="true"></i>
			</a>
		<?endif;?>
		<?if(!empty($arParams["LINK_YOUTUBE"])):?>
			<a class="social-icon" href="<?=$arParams['LINK_YOUTUBE']?>" target="_blank" rel="nofollow" title="Youtube">
				<i class="fa fa-youtube-play " aria-hidden="true"></i>
			</a>
		<?endif;?>
		<?if(!empty($arParams["LINK_ODNIKLASSNIKI"])):?>
			<a class="social-icon" href="<?=$arParams['LINK_ODNIKLASSNIKI']?>" target="_blank" rel="nofollow" title="Odnoklassniki">
				<i class="fa fa-odnoklassniki" aria-hidden="true"></i>
			</a>
		<?endif;?>
		<?if(!empty($arParams["LINK_GOOGLEPLUS"])):?>
			<a class="social-icon" href="<?=$arParams['LINK_GOOGLEPLUS']?>" target="_blank" rel="nofollow" title="Google Plus">
				<i class="fa fa-google-plus" aria-hidden="true"></i>
			</a>
		<?endif;?>
	</div>
<?endif;?>
<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class ReduxNoticeField
{
	/**
	 * @param Boolean $warning
	 * @param String $emphasize
	 * @param String $text
	 *
	 * @return String
	 */
	public static function getField($warning,$emphasize,$text)
	{		
		 ob_start();
?>
		<style>
			.redux-notice-field.redux-warning {
				border-right: 4px solid <?php echo $warning ? /*warning*/ '#fdce43' : /*error*/ '#fd4343' ?>;
				border-left: none;
			}
			.redux-notice-field {
				margin: 15px 0 0;
				background-color: #fff;
				border: 0;
				box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
				padding: 1px 12px;
			}
			.redux-notice-field .redux-info-desc {
				display: inline-block;
				vertical-align: top;
				margin: 0.5em 0;
				padding: 2px;
			}
		</style>
		<div id="info-mweb_banner_info" class="redux-warning redux-notice-field redux-field-info">
			<p class="redux-info-desc">
				<b><?php echo $emphasize ?></b><br><?php echo $text ?>
			</p>
		</div>
	<?php

		$noticeField = ob_get_clean();
		
		return $noticeField;
	}
}
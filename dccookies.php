<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  System.dccookies
 *
 * @copyright   Copyright (C) 2025 Design Cart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class PlgSystemDccookies extends CMSPlugin
{
	protected $app;

	public function onAfterRender()
	{
		if ($this->app->isClient('administrator')) {
			return;
		}

        $input = Factory::getApplication()->input;

		$cookieName = 'dc_cookie_accepted';

		if ($input->cookie->get($cookieName)) {
			return;
		}

		$doc = Factory::getDocument();
		$html = $this->getConsentHtml();
		$css = Uri::root() . 'plugins/system/dccookies/media/css/cookie.css';
		$jsTag = "<script>
            document.addEventListener('DOMContentLoaded', function () {
                const bar = document.getElementById('dc-cookies');
                if (!bar) return;
                bar.querySelector('.accept').addEventListener('click', function () {
                    document.cookie = 'dc_cookie_accepted=1; path=/; max-age=" . ((int) $this->params->get('duration', 365) * 86400) . "';
                    bar.remove();
                });
            });
            </script>";

		$buffer = $this->app->getBody();
		$buffer = str_ireplace('</body>', '<link rel="stylesheet" href="' . $css . '">' . $html . $jsTag . '</body>', $buffer);
		$this->app->setBody($buffer);
	}

	protected function getConsentHtml()
	{
		$params = $this->params;

		ob_start(); ?>
		<div id="dc-cookies" style="--bg: <?= $params->get('panel_bg'); ?>; --title: <?= $params->get('title_color'); ?>; --text: <?= $params->get('text_color'); ?>; --accept-bg: <?= $params->get('accept_bg'); ?>; --accept-color: <?= $params->get('accept_color'); ?>; --accept-hover-bg: <?= $params->get('accept_hover_bg'); ?>; --accept-hover-color: <?= $params->get('accept_hover_color'); ?>; --more-bg: <?= $params->get('more_bg'); ?>; --more-color: <?= $params->get('more_color'); ?>; --more-hover-bg: <?= $params->get('more_hover_bg'); ?>; --more-hover-color: <?= $params->get('more_hover_color'); ?>;">
			<?php if ($img = $params->get('image')) : ?>
				<img src="<?= Uri::root() . $img; ?>" alt="Icon" />
			<?php endif; ?>
			<h4><?= $params->get('title'); ?></h4>
			<p><?= nl2br($params->get('message')); ?></p>
			<div class="buttons">
				<button class="accept"><?= $params->get('accept_text'); ?></button>
				<a href="<?= $params->get('more_link'); ?>" target="_blank" class="more"><?= $params->get('more_text'); ?></a>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

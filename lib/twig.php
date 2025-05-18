<?php

const SAFE_HTML = ['is_safe' => ['html']];

class ForumExtension extends \Twig\Extension\AbstractExtension {

	public function getFunctions() {
		global $profiler;
		return [
			new \Twig\TwigFunction('profiler_stats', function () use ($profiler) {
				$profiler->getStats();
			}),

			new \Twig\TwigFunction('userlink', 'userlink', SAFE_HTML),
			new \Twig\TwigFunction('threadpost', 'threadpost', SAFE_HTML),
			new \Twig\TwigFunction('minipost', 'minipost', SAFE_HTML),
			new \Twig\TwigFunction('postform', 'postform', SAFE_HTML),
			new \Twig\TwigFunction('posttoolbar', 'posttoolbar', SAFE_HTML),

			new \Twig\TwigFunction('timelinks', 'timelinks', SAFE_HTML),

			new \Twig\TwigFunction('field_input', 'fieldinput', SAFE_HTML),
			new \Twig\TwigFunction('field_textarea', 'fieldtextarea', SAFE_HTML),
			new \Twig\TwigFunction('field_checkbox', 'fieldcheckbox', SAFE_HTML),
			new \Twig\TwigFunction('field_select', 'fieldselect', SAFE_HTML),

			new \Twig\TwigFunction('thread_status', 'threadStatus', SAFE_HTML),
			new \Twig\TwigFunction('theme_list', 'themeList', SAFE_HTML),

			new \Twig\TwigFunction('forumlist', 'forumlist', SAFE_HTML),

			new \Twig\TwigFunction('__', '__', SAFE_HTML)
		];
	}

	public function getFilters() {
		return [
			new \Twig\TwigFilter('postfilter', 'postfilter', SAFE_HTML),
			new \Twig\TwigFilter('relative_time', 'relTime'),
		];
	}
}

<?php

class ForumExtension extends \Twig\Extension\AbstractExtension {
	public function getFunctions() {
		global $profiler;
		return [
			new \Twig\TwigFunction('profiler_stats', function () use ($profiler) {
				$profiler->getStats();
			}),

			new \Twig\TwigFunction('userlink', 'userlink', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('threadpost', 'threadpost', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('postform', 'postform', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('posttoolbar', 'posttoolbar', ['is_safe' => ['html']]),

			new \Twig\TwigFunction('timelinks', 'timelinks', ['is_safe' => ['html']]),

			new \Twig\TwigFunction('field_input', 'fieldinput', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('field_textarea', 'fieldtextarea', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('field_checkbox', 'fieldcheckbox', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('field_select', 'fieldselect', ['is_safe' => ['html']]),

			new \Twig\TwigFunction('thread_status', 'threadStatus', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('theme_list', 'themeList', ['is_safe' => ['html']]),

			new \Twig\TwigFunction('forumlist', 'forumlist', ['is_safe' => ['html']]),

			new \Twig\TwigFunction('__', '__', ['is_safe' => ['html']])
		];
	}

	public function getFilters() {
		return [
			new \Twig\TwigFilter('postfilter', 'postfilter', ['is_safe' => ['html']]),
			new \Twig\TwigFilter('relative_time', 'relTime'),
		];
	}
}

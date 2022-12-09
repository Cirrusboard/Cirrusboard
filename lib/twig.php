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
			new \Twig\TwigFunction('posttoolbar', 'posttoolbar', ['is_safe' => ['html']]),

			new \Twig\TwigFunction('timelinks', 'timelinks', ['is_safe' => ['html']]),
		];
	}

	public function getFilters() {
		return [
			new \Twig\TwigFilter('postfilter', 'postfilter', ['is_safe' => ['html']]),
		];
	}
}

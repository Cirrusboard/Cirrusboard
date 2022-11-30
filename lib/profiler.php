<?php

class Profiler {
	private $starttime;

	function __construct() {
		$this->starttime = microtime(true);
	}

	function getStats() {
		printf("Rendered in %1.3f ms with %dKB memory used", (microtime(true) - $this->starttime) * 1000, memory_get_usage(false) / 1024);
	}
}

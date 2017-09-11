<?php 

namespace ACWPD;

class RequestThrottler {
	public static function delayPerAdvisory(int $delay) {
		usleep($delay + 100);
		return true;
	}
}
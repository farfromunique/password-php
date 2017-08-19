<?php 

namespace ACWPD;

class RequestThrottler {
	public function delayPerAdvisory(int $delay) {
		usleep($delay + 100);
		return true;
	}
}
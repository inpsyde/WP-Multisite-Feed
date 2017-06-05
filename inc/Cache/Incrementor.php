<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache;

interface Incrementor {

	public function get();

	public function increase();
}
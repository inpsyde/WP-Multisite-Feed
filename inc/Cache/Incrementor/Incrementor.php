<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache\Incrementor;

interface Incrementor {

	public function get();

	public function increase();
}

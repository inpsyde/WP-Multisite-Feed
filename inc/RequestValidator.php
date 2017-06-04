<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

interface RequestValidator {

	/**
	 * @return bool
	 */
	public function validate();
}
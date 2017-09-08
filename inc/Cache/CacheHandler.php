<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache;

interface CacheHandler
{

    public function get($key);

    public function set($key, $value);

    public function has($key);

    public function flush();
}

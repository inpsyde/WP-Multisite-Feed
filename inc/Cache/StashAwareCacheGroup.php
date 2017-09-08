<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache;

use Stash\Pool;

class StashAwareCacheGroup implements CacheHandler
{

    const GLUE = '/';
    /**
     * @var string
     */
    private $group_name;
    /**
     * @var string
     */
    private $pool;

    /**
     * Cache constructor.
     *
     * @param string $group_name
     * @param Pool   $pool
     */
    public function __construct($group_name, Pool $pool)
    {

        $this->pool       = $pool;
        $this->group_name = $group_name;
    }

    /**
     * Increment the incrementor and thereby invalidate the cache.
     */
    public function flush()
    {

        $this->pool->deleteItem($this->group_name);
    }

    public function get($key)
    {

        $item = $this->pool->getItem($this->get_key($key));

        return $item->isHit() ? $item->get() : null;
    }

    private function get_key($key)
    {

        return $this->group_name . self::GLUE . $key;
    }

    public function set($key, $value)
    {

        $item = $this->pool->getItem($this->get_key($key));
        $item->set($this->get_key($key), $value);
        $item->save();
    }

    public function has($key)
    {

        $item = $this->pool->getItem($this->get_key($key));

        return $item->isHit();

    }
}

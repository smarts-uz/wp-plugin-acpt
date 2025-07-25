<?php

namespace VendorDuplicator;

/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Abstract storage class
 *
 * @author Chris Chabot <chabotc@google.com>
 * @internal
 */
abstract class Google_Cache_Abstract
{
    public abstract function __construct(Google_Client $client);
    /**
     * Retrieves the data for the given key, or false if they
     * key is unknown or expired
     *
     * @param String $key The key who's data to retrieve
     * @param boolean|int $expiration Expiration time in seconds
     *
     */
    public abstract function get($key, $expiration = \false);
    /**
     * Store the key => $value set. The $value is serialized
     * by this function so can be of any type
     *
     * @param string $key Key of the data
     * @param string $value data
     */
    public abstract function set($key, $value);
    /**
     * Removes the key/data pair for the given $key
     *
     * @param String $key
     */
    public abstract function delete($key);
}
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Abstract storage class
 *
 * @author Chris Chabot <chabotc@google.com>
 * @internal
 */
\class_alias('VendorDuplicator\\Google_Cache_Abstract', 'VendorDuplicator\\Google_Cache_Abstract', \false);

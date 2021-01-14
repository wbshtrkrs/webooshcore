<?php

if (!function_exists('asset_with_version')) {
    /**
     * The function for generate path of public resource with version
     *
     * @param string $path
     * @param bool|null $secure
     * @return mixed
     */
    function asset_with_version(string $path, bool $secure = null) {
        return asset($path . "?v=" . md5_file(public_path($path)), $secure);
    }
}

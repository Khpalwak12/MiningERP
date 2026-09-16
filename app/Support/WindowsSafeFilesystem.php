<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;

/**
 * Windows-safe filesystem helpers for desktop installs.
 *
 * Laravel's Filesystem::replace() uses rename() which often fails on Windows
 * with "Access is denied" when antivirus locks compiled Blade view files.
 * Direct writes avoid that rename race entirely.
 */
class WindowsSafeFilesystem extends Filesystem
{
    public function replace($path, $content, $mode = null)
    {
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $written = @file_put_contents($path, $content, LOCK_EX);

        if ($written === false) {
            $tempPath = $directory.DIRECTORY_SEPARATOR.uniqid('view_', true).'.tmp';
            $written = file_put_contents($tempPath, $content);

            if ($written === false) {
                throw new \RuntimeException("Unable to write file to [{$path}].");
            }

            if (is_file($path)) {
                @unlink($path);
            }

            if (! @rename($tempPath, $path) && ! @copy($tempPath, $path)) {
                @unlink($tempPath);

                throw new \RuntimeException("Unable to write file to [{$path}].");
            }

            @unlink($tempPath);
        }

        if (! is_null($mode)) {
            @chmod($path, $mode);
        }
    }

    public function move($path, $target)
    {
        if (is_file($target)) {
            @unlink($target);
        }

        if (@rename($path, $target)) {
            return true;
        }

        if (@copy($path, $target)) {
            @unlink($path);

            return true;
        }

        return false;
    }
}

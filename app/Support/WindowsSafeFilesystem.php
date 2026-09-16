<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;

/**
 * Windows-safe filesystem helpers for desktop installs.
 *
 * Laravel's Filesystem::replace() uses rename() which often fails on Windows
 * with "Access is denied" when antivirus or another process briefly locks the
 * destination compiled view file.
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

        $tempPath = tempnam($directory, basename($path));

        if ($tempPath === false) {
            $tempPath = $directory.DIRECTORY_SEPARATOR.uniqid(basename($path), true).'.tmp';
        }

        if (! is_null($mode)) {
            @chmod($tempPath, $mode);
        } else {
            @chmod($tempPath, 0777 - umask());
        }

        file_put_contents($tempPath, $content);

        if (is_file($path)) {
            @unlink($path);
        }

        if (@rename($tempPath, $path)) {
            return;
        }

        if (! @copy($tempPath, $path)) {
            @unlink($tempPath);

            throw new \RuntimeException("Unable to write file to [{$path}].");
        }

        @unlink($tempPath);
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

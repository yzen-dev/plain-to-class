<?php

namespace Tests;

use InvalidArgumentException;

trait ClearCache
{
    public function clearCache()
    {
        if (file_exists(__DIR__ . '/../.cache')) {
            $this->deleteDir(__DIR__ . '/../.cache');
        } else {
            if (file_exists(__DIR__ . '/../vendor/yzen.dev/plain-to-class/.cache')) {
                $this->deleteDir(__DIR__ . '/../vendor/yzen.dev/plain-to-class/.cache');
            }
        }
    }

    private function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (!str_ends_with($dirPath, '/')) {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);

        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

}

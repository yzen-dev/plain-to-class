<?php

declare(strict_types=1);

namespace Tests;

/**
 *
 */
trait ClearCache
{
    /**
     * @return void
     */
    public function clearCache(): void
    {
        if (file_exists(__DIR__ . '/../.cache')) {
            $this->deleteDir(__DIR__ . '/../.cache');
        } elseif (file_exists(__DIR__ . '/../vendor/yzen.dev/plain-to-class/.cache')) {
            $this->deleteDir(__DIR__ . '/../vendor/yzen.dev/plain-to-class/.cache');
        }
    }

    /**
     * @param string $dirPath
     *
     * @return void
     */
    private function deleteDir(string $dirPath): void
    {
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

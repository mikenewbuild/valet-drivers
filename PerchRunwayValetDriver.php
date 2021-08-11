<?php

class PerchRunwayValetDriver extends BasicValetDriver
{
    protected $publicFolder = '/public';

    protected $start = '/core/runway/start.php';

    protected $perchFolders = ['/perch', '/cms'];

    protected $perchFolder = '';

    /**
     * Determine if the driver serves the request.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        foreach ($this->perchFolders as $perchFolder) {
            if ($this->isPerchRunway($sitePath, $perchFolder)) {
                $this->perchFolder = $perchFolder;
            }
        }

        return $this->perchFolder !== '';
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        $staticFilePath = $sitePath . $this->publicFolder . $uri;

        if (is_dir($staticFilePath)) {
            return false;
        }

        if (!file_exists($staticFilePath)) {
            return false;
        }

        return $staticFilePath;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        if (!$this->inPerchAdmin($uri)) {
            return $sitePath . $this->publicFolder . $this->perchFolder . $this->start;
        }

        $this->enablePerchAdminForms($uri);

        return $this->perchAdminUri($sitePath, $uri);
    }

    protected function perchAdminUri($sitePath, $uri)
    {
        $adminUri = $sitePath . $this->publicFolder . $uri;

        if (strpos($adminUri, 'index.php') === false) {
            $adminUri = $adminUri . '/index.php';
        }

        return $adminUri;
    }

    protected function inPerchAdmin($uri)
    {
        return $this->stringStartsWith($uri, $this->perchFolder);
    }

    protected function stringStartsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    protected function enablePerchAdminForms($uri)
    {
        $_SERVER['SCRIPT_NAME'] = $uri;
    }

    protected function isPerchRunway($sitePath, $folder)
    {
        return file_exists($sitePath . $this->publicFolder . $folder . $this->start);
    }
}

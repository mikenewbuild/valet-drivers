<?php

class PerchRunwayValetDriver extends BasicValetDriver
{
    protected $candidates = ['perch', 'cms', 'public/perch', 'public/cms'];

    protected $start = '/core/runway/start.php';

    protected $folder = '';

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
        foreach ($this->candidates as $folder) {
            if ($this->isPerchRunway($sitePath, $folder)) {
                $this->folder = $folder;
            }
        }

        return $this->folder !== '';
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
        $staticFilePath = $this->staticFilePath($sitePath, $uri);

        if (file_exists($staticFilePath)) {
            return $staticFilePath;
        }

        return false;
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
        return $sitePath . '/' . $this->folder . $this->start;
    }

    protected function staticFilePath($sitePath, $uri)
    {
        if (strpos($this->folder, 'public') !== false) {
            return $sitePath . '/public/' . $uri;
        }

        return $sitePath . '/' . $uri;
    }
    protected function isPerchRunway($sitePath, $folder)
    {
        return file_exists($sitePath . '/' . $folder . $this->start);
    }
}

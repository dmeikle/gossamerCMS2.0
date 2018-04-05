<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/1/2017
 * Time: 9:55 PM
 */

namespace Gossamer\Essentials\Configuration;


class SiteParams
{

    protected $sitePath;

    protected $configPath;

    protected $logPath;

    protected $siteName;

    protected $cacheDirectory;

    protected $debugOutputPath;

    protected $siteConfig;

    protected $isCaching;

    /**
     * @return mixed
     */
    public function getSiteConfig() {
        return $this->siteConfig;
    }

    /**
     * @param mixed $siteConfig
     * @return SiteParams
     */
    public function setSiteConfig($siteConfig) {
        $this->siteConfig = $siteConfig;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsCaching() {
        return $this->isCaching;
    }

    /**
     * @param mixed $isCaching
     * @return SiteParams
     */
    public function setIsCaching($isCaching) {
        $this->isCaching = $isCaching;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getSitePath() {
        return $this->sitePath;
    }

    /**
     * @param mixed $sitePath
     * @return SiteParams
     */
    public function setSitePath($sitePath) {
        $this->sitePath = $sitePath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfigPath() {
        return $this->configPath;
    }

    /**
     * @param mixed $configPath
     * @return SiteParams
     */
    public function setConfigPath($configPath) {
        $this->configPath = $configPath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogPath() {
        return $this->logPath;
    }

    /**
     * @param mixed $logPath
     * @return SiteParams
     */
    public function setLogPath($logPath) {
        $this->logPath = $logPath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiteName() {
        return $this->siteName;
    }

    /**
     * @param mixed $siteName
     * @return SiteParams
     */
    public function setSiteName($siteName) {
        $this->siteName = $siteName;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getCacheDirectory() {
        return $this->cacheDirectory;
    }

    /**
     * @param mixed $cacheDirectory
     * @return SiteParams
     */
    public function setCacheDirectory($cacheDirectory) {
        $this->cacheDirectory = $cacheDirectory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDebugOutputPath() {
        return $this->debugOutputPath;
    }

    /**
     * @param mixed $debugOutputPath
     * @return SiteParams
     */
    public function setDebugOutputPath($debugOutputPath) {
        $this->debugOutputPath = $debugOutputPath;
        return $this;
    }


}
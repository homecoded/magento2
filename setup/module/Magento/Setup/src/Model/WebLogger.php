<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Web UI Logger
 *
 * @package Magento\Setup\Model
 */
class WebLogger implements LoggerInterface
{
    /**
     * Log File
     *
     * @const string
     */
    const LOG_WEB = 'install.log';

    /**
     * Currently open file resource
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Currently open file resource
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * Indicator of whether inline output is started
     *
     * @var bool
     */
    private $isInline = false;

    /**
     * Constructor
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::LOG);
    }

    /**
     * {@inheritdoc}
     */
    public function logSuccess($message)
    {
        $this->terminateLine();
        $this->writeToFile('<span class="text-success">[SUCCESS] ' . $message . '</span><br/>');
    }

    /**
     * {@inheritdoc}
     */
    public function logError(\Exception $e)
    {
        $this->terminateLine();
        $this->writeToFile('<span class="text-danger">[ERROR] ' . $e . '<span><br/>');
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        $this->terminateLine();
        $this->writeToFile('<span class="text-info">' . $message . '</span><br/>');
    }

    /**
     * {@inheritdoc}
     */
    public function logInline($message)
    {
        $this->isInline = true;
        $this->writeToFile('<span class="text-info">' . $message . '</span>');
    }

    /**
     * {@inheritdoc}
     */
    public function logMeta($message)
    {
        $this->terminateLine();
        $this->writeToFile('<span class="hidden">' . $message . '</span><br/>');
    }

    /**
     * Write the message to file
     *
     * @param string $message
     * @return void
     */
    private function writeToFile($message)
    {
        $this->directory->writeFile(self::LOG_WEB, $message, 'a+');
    }

    /**
     * Gets contents of the log
     *
     * @return array
     */
    public function get()
    {
        $fileContents = explode('\n', $this->directory->readFile(self::LOG_WEB));
        return $fileContents;
    }

    /**
     * Clears contents of the log
     *
     * @return void
     */
    public function clear()
    {
        if ($this->directory->isExist(self::LOG_WEB)) {
            $this->directory->delete(self::LOG_WEB);
        }
    }

    /**
     * Terminates line if the inline logging is started
     *
     * @return void
     */
    private function terminateLine()
    {
        if ($this->isInline) {
            $this->isInline = false;
            $this->writeToFile('</br>');
        }
    }
}

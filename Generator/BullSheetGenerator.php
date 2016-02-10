<?php

namespace BullSheet\Generator;

/*
 * LingTalfi 2016-02-10
 */
use Bat\LocalHostTool;
use DirScanner\YorgDirScannerTool;

class BullSheetGenerator implements BullSheetGeneratorInterface
{


    private $dir;
    private $fileLists;


    public function __construct()
    {
        $this->fileLists = [];
    }


    public static function create()
    {
        return new static();
    }


    public function getPureData($domain = null): string
    {
        $fileList = $this->getFileList($domain);
        $file = $fileList[array_rand($fileList)];
        return $this->getRandomLine($file);
    }


    public function setDir($dir)
    {
        $this->dir = $dir;
        return $this;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getDir()
    {
        return $this->dir;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function getRandomLine($file): string
    {
        if (false && LocalHostTool::isUnix()) {

            /**
             * I use unix tools first, because they are generally faster
             *
             * http://stackoverflow.com/questions/2162497/efficiently-counting-the-number-of-lines-of-a-text-file-200mb
             *
             */
            $sFile = '"' . str_replace('"', '\"', $file) . '"';
            $out = trim(exec('wc -l ' . $sFile));
            $nbLines = (int)substr($out, 0, strpos($out, ' ')) + 1;

            $randLine = mt_rand(1, $nbLines);

            $line = exec('tail -n+' . $randLine . ' ' . $sFile . ' | head -n1');
        }
        else {
            /**
             * http://stackoverflow.com/questions/12118995/how-to-echo-random-line-from-text-file
             */
            $maxLineLength = 4096;
            $handle = @fopen($file, "r");
            if ($handle) {
                $random_line = null;
                $line = null;
                $count = 0;
                while (($theline = fgets($handle, $maxLineLength)) !== false) {
                    $count++;
                    // P(1/$count) probability of picking current line as random line
                    if (mt_rand() % $count == 0) {
                        $line = $theline;
                    }
                }
                if (!feof($handle)) {
                    fclose($handle);
                    throw new BullSheetException("Error: unexpected fgets() fail");
                }
                else {
                    fclose($handle);
                }
                /**
                 * remove the line carriage return that sometimes get appended
                 */
                $line = trim($line);
            }
        }
        return $line;
    }


    private function getFileList($domain = null): array
    {
        if (is_array($domain)) {
            $hashIndex = implode('', $domain);
        }
        else {
            $hashIndex = (string)$domain;
        }
        if (array_key_exists($hashIndex, $this->fileLists)) {
            $fList = $this->fileLists[$hashIndex];
        }
        else {
            $fList = [];
            if (is_array($domain)) {
                foreach ($domain as $dom) {
                    $this->collectDataFiles($dom, $fList);
                }
            }
            else {
                $this->collectDataFiles((string)$domain, $fList);
            }
            $this->fileLists[$hashIndex] = $fList;
        }
        return $fList;
    }

    private function collectDataFiles(string $dom, array &$fileList, $silent = false)
    {
        // resolving wildcard if any
        if (false !== ($pos = strpos($dom, '/*/'))) {

            $rPath = substr($dom, 0, $pos);
            $baseDir = $this->getDir() . "/" . $rPath;
            $suffix = substr($dom, $pos + 3);

            if (is_dir($baseDir)) {
                $domains = scandir($baseDir);


                foreach ($domains as $domain) {
                    if (
                        '.' !== $domain &&
                        '..' !== $domain &&
                        '.' !== substr($domain, 0, 1)  // removing the hidden files too (.DS_Store, ...)
                    ) {
                        $rdom = $rPath . '/' . $domain;
                        if (strlen($suffix) > 0) {
                            $rdom .= '/' . $suffix;
                        }
                        /**
                         * We call silent mode, because it happens that one of the rdom try is not a directory.
                         * For instance, if you have this structure:
                         *
                         * - first_name/
                         * ----- 1967/
                         * --------- male/
                         * --------- female/
                         * ----- 1968/
                         * --------- male/
                         * --------- female/
                         * ----- all/
                         *
                         * You see that the all directory does not contain a male or female directory,
                         * so a call to this domain
                         *
                         *          first_name/(wildcard)/male
                         *
                         * will successfully try the
                         *
                         *          first_name/1967/male
                         * and
                         *          first_name/1968/male
                         * directories,
                         * but also fail trying the non existing first_name/all/male directory.
                         *
                         */
                        $this->collectDataFiles($rdom, $fileList, true);
                    }
                }
            }
            else {
                trigger_error("This is not a directory: $baseDir (expanded from $dom)", E_USER_WARNING);
            }
        }
        else {
            $f = $this->getDir() . "/" . $dom;
            if (is_dir($f)) {
                $files = YorgDirScannerTool::getFilesWithExtension($f, 'txt', false, true);
                $fileList = array_merge($fileList, $files);
            }
            else {
                if (false === $silent) {
                    trigger_error("This is not a directory: $f", E_USER_WARNING);
                }
            }
        }
    }


}

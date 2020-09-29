<?php


/**
* Script transforms PHP code files into highlighted HTML 
*
* Script should be executed from the command line, simplest usage is:
* php php-to-html.php
*
* It will then scan the directory it is in, find all php files, 
* create an "html" subdirectory and place html files containing highlighted 
* PHP code in it.
*
* Example usage to recurse subdirectories, specify input and output directories:
* php php-to-html.php --recurse --dir /path/to/code --output /path/to/save/html
*/

/**
* Global - input directory with PHP files
*/
global $dir;
$dir = realpath(dirname(__FILE__));

/**
* Global - output directory that will hold highlighted HTML files
*/
global $outDir;
$outDir = realpath(dirname(__FILE__)) .  "/html";

$recurse = false;

/**
* Parse command-line arguments
*/
for($i = 1; $i < count($argv); $i++) {
    $argument = $argv[$i];

    switch ($argument) {
        case "--dir":
            $dir = $argv[$i + 1];
            break;
        case "--output":
            $outDir = $argv[$i + 1];
            break;
        case "--recurse":
            $recurse = true;
            break;
    }
}


if (!file_exists($outDir)) mkdir($outDir);

function processDir($dirPath, $recurse) {
    global $dir;
    global $outDir;

    $files = scandir($dirPath);

    // subdir path part, to add to global $outDir:
    $outSubDir = str_replace($dir, "", $dirPath);

    foreach ($files as $file) {
        if ($file == "." || $file == "..") continue;

        if (is_dir($dirPath."/".$file)) {
            processDir($dirPath."/".$file, $recurse);
            continue;
        }

        #echo "File is: $file and full path is: $dirPath/$file \n";

        $pathInfo = pathinfo($dirPath."/".$file);

        if ($pathInfo['extension'] == "php") {
            $output = highlight_file($dirPath."/".$file, true);
            if ($outSubDir != "") {
                $outputFolder = $outDir . "/" . $outSubDir . "/";
            } else {
                $outputFolder = $outDir . "/";
            }
            $fileToWrite = $outputFolder.$pathInfo['basename'].".html";
            var_dump($outputFolder, $fileToWrite);

            if (!file_exists($outputFolder)) mkdir($outputFolder);

            file_put_contents($fileToWrite, $output);
        }
    }

}


processDir($dir, $recurse, true);

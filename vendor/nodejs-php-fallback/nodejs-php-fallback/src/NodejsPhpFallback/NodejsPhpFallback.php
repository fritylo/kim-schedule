<?php

namespace NodejsPhpFallback;

use Composer\Composer;
use Composer\EventDispatcher\Event;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use ErrorException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class NodejsPhpFallback
{
    protected static $modulePaths = [];
    protected static $maxInstallRetry = 3;
    protected $nodePath;

    public function __construct($nodePath = null)
    {
        $this->nodePath = $nodePath ?: 'node';
    }

    public static function setModulePath($module, $path)
    {
        static::$modulePaths[$module] = $path;
    }

    public static function setMaxInstallRetry($count)
    {
        static::$maxInstallRetry = $count;
    }

    public static function forgetConfirmRemindedChoice()
    {
        $remindedChoice = static::getConfirmRemindedChoiceFile();

        if (file_exists($remindedChoice)) {
            unlink($remindedChoice);
        }
    }

    protected static function getConfirmRemindedChoiceFile()
    {
        return __DIR__.'/npm-confirm-reminded-choice.txt';
    }

    protected static function getPackagesList(Event $event, array $config, array $npm): array
    {
        /** @var Composer $composer */
        $composer = $event->getComposer();
        $npmConfirm = static::getNpmConfig($composer, 'npm-confirm');

        if (isset($config['npm-confirm'])) {
            $npmConfirm = array_merge($npmConfirm, (array) $config['npm-confirm']);
        }

        if (count($npmConfirm)) {
            $npm = static::askForInstall($event, $npmConfirm, $npm);
        }

        return $npm;
    }

    public static function install(Event $event)
    {
        /** @var Composer $composer */
        $composer = $event->getComposer();
        $npm = static::getNpmConfig($composer);
        /** @var array $config */
        $config = $composer->getPackage()->getExtra();
        /** @var IOInterface $io */
        $io = $event->getIO();

        if (!count($npm)) {
            $io->write(
                isset($config['npm'])
                    ? 'No packages found.'
                    : "Warning: in order to use NodejsPhpFallback, you should add a 'npm' setting in your composer.json"
            );

            return;
        }

        $npm = static::getPackagesList($event, $config, $npm);

        if (count($npm)) {
            static::installPackages($npm, function ($install) use ($io) {
                $io->write('Package added to be installed/updated with npm: '.$install);
            })
                ? $io->write('Packages installed.')
                : $io->writeError('Installation failed after '.static::$maxInstallRetry.' tries.');
        }
    }

    protected static function getNpmConfig(Composer $composer, $key = null)
    {
        $vendorDir = $composer->getConfig()->get('vendor-dir');

        $npm = [];

        foreach (scandir($vendorDir) as $namespace) {
            if ($namespace === '.' || $namespace === '..' || !is_dir($directory = $vendorDir.DIRECTORY_SEPARATOR.$namespace)) {
                continue;
            }

            foreach (scandir($directory) as $dependency) {
                if ($dependency === '.' || $dependency === '..' || !is_dir($subDirectory = $directory.DIRECTORY_SEPARATOR.$dependency)) {
                    continue;
                }

                static::appendConfig($npm, $subDirectory, $key);
            }
        }

        static::appendConfig($npm, dirname($vendorDir), $key);

        return $npm;
    }

    protected static function appendConfig(&$npm, $directory, $key = null)
    {
        $json = new JsonFile($directory.DIRECTORY_SEPARATOR.'composer.json');
        $key = $key ? $key : 'npm';

        try {
            $dependencyConfig = $json->read();
        } catch (RuntimeException $e) {
            $dependencyConfig = null;
        }

        if (is_array($dependencyConfig) && isset($dependencyConfig['extra'], $dependencyConfig['extra'][$key])) {
            $npm = array_merge($npm, (array) $dependencyConfig['extra'][$key]);
        }
    }

    public static function askForInstall(Event $event, $npmConfirm, $npm)
    {
        $io = $event->getIO();

        if (!$io->isInteractive()) {
            return $npm;
        }

        $count = count($npmConfirm);
        $packageWord = $count > 1 ? 'packages' : 'package';
        $manual = static::getGlobalInstallChoice(
            $io,
            "$count node $packageWord can be optionally installed/updated.\n".
            "  - Enter Y to install/update them automatically on composer install/update.\n".
            "  - Enter N to ignore them and not asking again.\n".
            '  - Enter M to manually decide for each package at each run. [Y/N/M] '
        );
        $manual = ($manual === 'y' ? true : ($manual === 'n' ? false : null));

        $confirm = [];

        foreach ($npmConfirm as $package => $message) {
            $confirm[$package] = $manual === null ? $io->askConfirmation(
                "The node package [$package] can be installed:\n$message\n".
                "Would you like to install/update it? (if you're not sure, you can safely ".
                'press Y to get the package ready to use if you need it later) [Y/N] '
            ) : $manual;
        }

        $packages = [];

        foreach ($npm as $key => $value) {
            $package = is_int($key) ? $value : $key;

            if (!isset($confirm[$package]) || $confirm[$package]) {
                $packages[$key] = $value;
            }
        }

        return $packages;
    }

    protected static function getGlobalInstallChoice(IOInterface $io, $message)
    {
        $answer = getenv('NODEJS_PHP_FALLBACK_ANSWER');

        if (is_string($answer) && $answer !== '') {
            return $answer;
        }

        $remindedChoice = static::getConfirmRemindedChoiceFile();

        if (!file_exists($remindedChoice) || !is_readable($remindedChoice)) {
            try {
                $manual = strtolower($io->ask($message));
            } catch (Throwable $e) {
                return 'y';
            }

            @file_put_contents($remindedChoice, $manual);

            return $manual;
        }

        return file_get_contents($remindedChoice);
    }

    public static function installPackages($npm, $onFound = null)
    {
        if (!count($npm)) {
            return true;
        }

        $packages = '';
        $packageNames = [];

        foreach ($npm as $package => $version) {
            if (is_int($package)) {
                $package = $version;
                $version = '*';
            }

            $packageNames[] = $package;
            $install = $package.'@"'.addslashes($version).'"';

            if ($onFound) {
                call_user_func($onFound, $install);
            }

            $packages .= ' '.$install;
        }

        for ($i = static::$maxInstallRetry; $i > 0; $i--) {
            $result = shell_exec(
                'npm install --force --loglevel=error '.
                '--prefix '.escapeshellarg(static::getPrefixPath()).
                $packages.
                ' 2>&1'
            );

            if (strpos($result, 'npm ERR!') === false && static::isInstalledPackage($packageNames)) {
                return true;
            }
        }

        return false;
    }

    public static function isInstalledPackage($packages)
    {
        if (!is_array($packages)) {
            $packages = [$packages];
        }

        foreach ($packages as $package) {
            if (!file_exists(static::getNodeModule($package))) {
                return false;
            }
        }

        return true;
    }

    public function exec($script, $fallback = null)
    {
        return $this->execOrFallback($script, $fallback, false);
    }

    protected function execOrFallback($script, $fallback, $withNode)
    {
        $exec = $this->checkFallback($fallback)
            ? $this->shellExec($withNode)
            : $fallback;

        return call_user_func($exec, $script);
    }

    protected function checkFallback($fallback)
    {
        if ($this->isNodeInstalled()) {
            return true;
        }

        if (is_null($fallback)) {
            throw new ErrorException('Please install node.js or provide a PHP fallback.', 2);
        }

        if (!is_callable($fallback)) {
            throw new InvalidArgumentException('The fallback provided is not callable.', 1);
        }

        return false;
    }

    public function isNodeInstalled()
    {
        $exec = $this->shellExec(true);

        return substr($exec('--version'), 0, 1) === 'v';
    }

    protected function shellExec($withNode)
    {
        $prefix = $withNode ? $this->getNodePath().' ' : '';

        return function ($script) use ($prefix) {
            return shell_exec($prefix.$script.' 2>&1');
        };
    }

    public function getNodePath()
    {
        return $this->nodePath;
    }

    public function setNodePath($nodePath)
    {
        $this->nodePath = $nodePath;

        return $this;
    }

    public function execModuleScript($module, $script, $arguments, $fallback = null)
    {
        return $this->nodeExec(
            static::getModuleScript($module, $script).(empty($arguments) ? '' : ' '.$arguments),
            $fallback
        );
    }

    public function nodeExec($script, $fallback = null)
    {
        return $this->execOrFallback($script, $fallback, true);
    }

    public static function getModuleScript($module, $script)
    {
        $module = static::getNodeModule($module);
        $path = $module.DIRECTORY_SEPARATOR.$script;
        if (!file_exists($path)) {
            throw new InvalidArgumentException("The $script was not found in the module path $module.", 3);
        }

        return escapeshellarg(realpath($path));
    }

    public static function getNodeModule($module)
    {
        return empty(static::$modulePaths[$module])
            ? static::getNodeModules().DIRECTORY_SEPARATOR.$module
            : static::$modulePaths[$module];
    }

    public static function getNodeModules()
    {
        return static::getPrefixPath().DIRECTORY_SEPARATOR.'node_modules';
    }

    public static function getPrefixPath()
    {
        return dirname(dirname(__DIR__));
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Console
 *
 * @author eugen_siderka
 */
class Console {

    static $console;
    
    private $_isForced;
    private $_isDebug;
    private $_size;
    private $_operationStartTime;
    private $_operationStepCurrent;
    private $_operationStepLimit;
    private $_operationStepSymbol;
    private $_debugStartTime;
    private $_progressStartTime;
    private $_progressStepCurrent;
    private $_progressTotal;
    private $_debugOperationStepMark;
    private $_tableColumns;

    private function __construct($isForced = false, $isDebug = false) {
        $this->_isForced = $isForced;
        $this->_isDebug = $isDebug;
        $this->_size = array(
            'width' => exec("tput cols"),
            'height' => exec("tput lines"),
        );
        $this->_operationStartTime = 0;
        $this->_operationStepCurrent = 0;
        $this->_operationStepLimit = 3;
        $this->_operationStepSymbol = '.';
        $this->_debugOperationStepMark = '>';
    }
    
    public static function getInstance($isForced = false, $isDebug = false) {
        if (!self::$console) {
            self::$console = new Console($isForced, $isDebug);
        }
        
        return self::$console;
    }

    /**
     * Checks current environment for command line interface
     * @return boolean
     */
    public static function isConsoleApp() {
        return (php_sapi_name() === 'cli');
    }

    /**
     * Echoes string
     * @param string $message
     * @return false on error
     */
    public static function write($message) {
        if (!self::isConsoleApp()) {
            return false;
        }

        echo $message;
    }

    /**
     * Echoes string with end of line
     * @param string $message
     */
    public static function writeLine($message) {
        self::write($message . PHP_EOL);
    }

    /**
     * Echoes error-style string
     * @param string $message
     */
    public static function error($message) {
        echo PHP_EOL . '[Error] ' . $message . PHP_EOL;
    }

    /**
     * Echoes array of associative arrays formatted as table
     * @param array $data
     * @param int $width
     * @return boolean
     */
    public static function table($data, $width = false) {
        $columns = array();

        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                if (!in_array($key, $columns)) {
                    $columns[] = $key;
                }
            }
        }

        $columnsCount = count($columns);
        $dataCount = count($data);

        if ($colsSizePercentage) {
            if (count($colsSizePercentage) != $columnsCount) {
                self::error(String::build('Columns count did not match'));

                return false;
            }

            if (($precentage = array_sum($colsSizePercentage)) != 100) {
                self::error(String::build('Table size percentage is incorrect. ' . $precentage . '% received'));

                return false;
            }
        }

        $consoleSize = array(
            'x' => $width ? $width : exec("tput cols"),
            'y' => exec("tput lines"),
        );
        $colWidth = ($consoleSize['x'] - $columnsCount) / $columnsCount;
        $tableParams = array(
            'connector' => '#',
            'borderX' => '-',
            'borderY' => '|',
        );

        /**
         * Header
         */
        self::write($tableParams['connector']);

        for ($i = 0; $i < $columnsCount; $i++) {
            self::write(str_repeat($tableParams['borderX'], $colWidth) . $tableParams['connector']);
        }

        self::write(PHP_EOL);

        foreach ($columns as $key => $value) {
            $whitespaceCount = $colWidth - strlen($value);
            $whitespaceCountHalf = floor($whitespaceCount / 2);

            self::write($tableParams['borderY'] . str_repeat(' ', $whitespaceCountHalf) . strtoupper($value) . str_repeat(' ', $whitespaceCount - $whitespaceCountHalf));
        }

        self::write($tableParams['borderY'] . PHP_EOL);
        self::write($tableParams['connector']);

        for ($i = 0; $i < $columnsCount; $i++) {
            self::write(str_repeat($tableParams['borderX'], $colWidth) . $tableParams['connector']);
        }

        self::write(PHP_EOL);

        /**
         * Body
         */
        for ($i = 0; $i < $dataCount; $i++) {
            for ($j = 0; $j < $columnsCount; $j++) {
                $value = String::truncate($data[$i][$columns[$j]], $colWidth);
                $whitespaceCount = $colWidth - strlen($value);

                self::write($tableParams['borderY'] . $value . str_repeat(' ', $whitespaceCount));
            }

            self::write($tableParams['borderY'] . PHP_EOL);
        }

        /**
         * Footer
         */
        self::write($tableParams['connector']);

        for ($i = 0; $i < $columnsCount; $i++) {
            self::write(str_repeat($tableParams['borderX'], $colWidth) . $tableParams['connector']);
        }

        self::write(PHP_EOL);

        return true;
    }

    /**
     * Begin operation with one line style
     * @param string $message
     */
    public function operationStart($message) {
        $this->_operationStartTime = time();
        $this->_operationStepCurrent = 0;

        self::write('[Operation] ' . $message . $this->_operationStepSymbol);
    }

    /**
     * Echoes operation step symbol
     */
    public function operationStep() {
        if ($this->_isDebug) {
            return;
        }

        if ($this->_operationStepCurrent++ >= $this->_operationStepLimit) {
            $this->_operationStepCurrent = 0;

            self::write(str_repeat("\x08", $this->_operationStepLimit));
            self::write(str_repeat(' ', $this->_operationStepLimit));
            self::write(str_repeat("\x08", $this->_operationStepLimit));
        } else {
            self::write($this->_operationStepSymbol);
        }
    }

    /**
     * End operation and show elapsed time
     */
    public function operationEnd() {
        $elapsedTime = time() - $this->_operationStartTime;

        self::writeLine(' Done (elapsed time: ' . $elapsedTime . ' sec)');
    }

    /**
     * Start progreass bar
     * @param string $message
     * @param int $totalCount
     */
    public function progressStart($message, $totalCount) {
        $this->_progressStartTime = time();
        $this->_progressStepCurrent = 0;
        $this->_progressTotal = $totalCount;

        self::writeLine('[Progress] ' . $message);
        $this->progressStep();
    }

    /**
     * Progress bar step
     */
    public function progressStep() {
        $done = $this->_progressStepCurrent;
        $total = $this->_progressTotal;

        if (!$this->_isDebug) {
            $perc = ceil(($done / $total) * 100);

            $bar = "[" . str_repeat("=", $perc) . ">";
            $bar .= str_repeat(" ", 100 - $perc) . "] - $perc% - $done/$total";

            echo "\033[0G$bar";
        }

        if ($done >= $total) {
            echo PHP_EOL;
        } else {
            $this->_progressStepCurrent++;
        }
    }

    /**
     * End progress bar and show elapsed time
     */
    public function progressEnd() {
        self::writeLine(String::build(' Done with {percent}% (elapsed time: {elapsed_time} sec)', array(
                    'percent' => ceil(($this->_progressStepCurrent / $this->_progressTotal) * 100),
                    'elapsed_time' => time() - $this->_progressStartTime,
        )));
    }

    /**
     * Echoes debug-style message only if isDebug is true
     * @param string $message
     * @return false on error
     */
    public function debug($message) {
        if (!$this->_isDebug) {
            return false;
        }

        self::writeLine(String::build('[Debug] {0} {1} {2}', array(
                    $this->timestamp(),
                    $this->_debugStartTime ? $this->_debugOperationStepMark : '',
                    $message,
        )));
    }

    /**
     * Start debug operation
     * @param string $message
     */
    public function debugStart($message) {
        $this->debug('[Operation] ' . $message);

        $this->_debugStartTime = time();
    }

    /**
     * End debug operation and show elapsed time
     */
    public function debugEnd() {
        $elapsedTime = time() - $this->_debugStartTime;
        $this->_debugStartTime = false;

        $this->debug('Operation ended (elapsed time: ' . $elapsedTime . ' sec)');
    }

    /**
     * Get microtime string
     * @return string
     */
    private function timestamp() {
        $microtime = microtime(true);
        $microtimeString = sprintf('%03d', ($microtime - floor($microtime)) * 1000);

        return String::build('{0}{1}: ', array(
                    gmdate('H:i:s.', $microtime),
                    $microtimeString,
        ));
    }

    /**
     * Echoes message for console with timestamp.
     * (will echo message only if app in console)
     * If message ends with '...' next message will appear in same line.
     * @param string $message Message string
     * @param bool $isError If TRUE - adds error label
     * @deprecated since version 1.4
     */
    public static function out($message, $isError = false) {
        if (php_sapi_name() !== 'cli') {
            return;
        }
        $microtime = microtime(true);
        $microtimeString = sprintf('%03d', ($microtime - floor($microtime)) * 1000);
        $ending = substr($message, strlen($message) - 3, 3);
        echo gmdate('H:i:s.', $microtime) . $microtimeString . ($isError ? ': Error! ' : ': ') . $message . ($ending == '...' ? ' ' : PHP_EOL);
    }

    /**
     * Echoes progress bar
     * @param int $done 'Done' count
     * @param int $total 'Total' count
     * @deprecated since version 1.5
     */
    public static function printPercent($done, $total) {
        $perc = ceil(($done / $total) * 100);
        $bar = "[" . str_repeat("=", $perc) . ">";
        $bar .= str_repeat(" ", 100 - $perc) . "] - $perc% - $done/$total";
        echo "\033[0G$bar";
        if ($done >= $total) {
            echo PHP_EOL;
        }
    }

}

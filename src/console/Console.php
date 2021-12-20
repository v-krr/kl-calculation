<?php

namespace vkrr\kblayout\calc\console;

/**
 * 
 */
class Console
{

    const CALC_OPTIONS = [
        ['langs::', 'Required. Define comma separated languages list for calculation'],
        ['m-summary', 'Monograms summary information'],
        ['b-summary', 'Bigrams summary information'],
        ['t-summary', 'Trigrams summary information'],
        ['b-column-usage', 'Usage keyboard columns by bigrams'],
        ['b-zones-usage', 'Usage keyboard zones by bigrams'],
        ['b-fingers-usage', 'Usage fingers by bigrams'],
        ['b-hands-alter', 'Hands alteration by bigrams'],
        ['b-top-reiter::', 'Top n bigrams reiterations'],
        ['t-hands-alter', 'Hands alteration by trigrams'],
        ['t-top-reiter::', 'Top n trigrams reiterations'],
    ];
    
    static array $options = [];

    /**
     * Run calculator from command line
     */
    public static function run()
    {
        global $argv;

        $longOpts = \array_column(self::CALC_OPTIONS, 0);
        $optIndex = null;
        self::$options = \getopt('', $longOpts, $optIndex);
        
        $args = \array_slice($argv, $optIndex);
        $confName = \array_shift($args);
        if (!$confName) {
            echo "Config file is reqiured\n\n";
            self::print_usage();
            exit();
        }
        
        $langsList = self::$options['langs'] ?? '';
        if (!$langsList) {
            echo "Languages list is empty\n\n";
            self::print_usage();
            exit();
        }
        
        $langs = explode(',', $langsList);

        try {
            self::calc($confName, $langs);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Execute calculation
     */
    static function calc(string $confName, array $langs) 
    {
        $conf = new YamlConfig($confName);
        $out = new ConsoleFormat();
    
        // find out what will be used
        $isMonogram = self::findOptionWith('m-');
        $isBigram = self::findOptionWith('b-');
        $isTrigram = self::findOptionWith('t-');


        foreach($langs as $ln) {
            $lang = $conf->getLang($ln);
            $map = new SignsMap($lang->mainLevel, $lang->altLevel);
    
            // if ($isMonogram) {
            //     if (!$lang->monogramsFile) {
            //         throw new \Exception(
            //             "Monograms file is missing in config for language '$ln'"
            //         );
            //     }

            //     $mon = new Monograms($map);
            //     $mon->calculate($lang->monogramsFile);
            //     $out->monograms($mon, self::$options);
            // }
    
            // if ($isBigram) {
            //     if (!$lang->bigramsFile) {
            //         throw new \Exception(
            //             "Bigrams file is missing in config for language '$ln'"
            //         );
            //     }
            //     $big = new Bigrams($map);
            //     $big->calculate($lang->bigramsFile);
            //     $out->bigrams($big, self::$options);
            // }
    
            // if ($isTrigram) {
            //     if (!$lang->trigramsFile) {
            //         throw new \Exception(
            //             "Trigrams file is missing in config for language '$ln'"
            //         );
            //     }
            //     $tri = new Trigrams($map);
            //     $tri->calculate($lang->trigramsFile);
            //     $out->trigrams($tri, self::$options);
            // }
        }
        
    }

    static function findOptionWith(string $needle):bool
    {
        foreach(self::$options as $it => $val) {
            if (str_starts_with($it, $needle)) return true;
        }

        return false;
    }

    static function print_usage() 
    {
        global $argv;
    
        printf("Usage: %s [options] config-yaml\n", $argv[0]);
        printf("Options:\n");
        array_map(function($it) {
            $opt = str_pad( trim($it[0], ':'), 20);
            printf("  --%s %s\n", $opt, $it[1]);
    
        }, self::CALC_OPTIONS);
    }
    
}
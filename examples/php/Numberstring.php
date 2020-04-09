<?php

namespace JPNut\Pearley\Examples;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Symbol;


/**
 * Class Numberstring
 *
 * Generated automatically by pearley
 * https://github.com/jpnut/pearley
 */
class Numberstring
{
    public static function grammar(): Grammar
    {
        $id = fn($x) => $x[0];

        $subId = fn($d) => $d[0][0];
        
        $nameMap = [
            'one' => 1,'two' => 2,'three' => 3,'four' => 4,'five' => 5,'six' => 6,'seven' => 7,'eight' => 8,'nine' => 9,
            'ten' => 10,'eleven' => 11,'twelve' => 12,'thirteen' => 13,'fourteen' => 14,'fifteen' => 15,'sixteen' => 16,'seventeen' => 17,'eighteen' => 18,'nineteen' => 19,
            'twenty' => 20,'thirty' => 30,'fourty' => 40,'fifty' => 50,'sixty' => 60,'seventy' => 70,'eighty' => 80,'ninety' => 90,
            'hundred' => 100,'thousand' => 1000,'million' => 1000000,'billion' => 1000000000
        ];
        
        $nameToVal = fn($d) => $nameMap[$d[0][0]];
        
        $nothing = fn($d) => null;
        
        $sumParts = function($d) {
            $retVal = 0;
        
            foreach ($d as $curVal) {
                if(!is_null($curVal) && is_numeric($curVal)) {
                    $retVal = $retVal + intval($curVal);
                }
            }
        
            return $retVal;
        };
        
        $multParts = function($d) {
            $retVal = 1;
        
            foreach ($d as $curVal) {
                if(!is_null($curVal) && is_numeric($curVal)) {
                    $retVal = $retVal * intval($curVal);
                }
            }
        
            return $retVal;
        };
        
        $findFirstNestedValueFromArray = function($d) use (&$findFirstNestedValueFromArray) {
            return is_array($d)
                ? $findFirstNestedValueFromArray($d[0])
                : $d;
        };

        return new Grammar([
            ['name' => 'main', 'symbols' => ['billionsNumber'], 'postprocess' => $findFirstNestedValueFromArray],
            ['name' => 'billionsNumber', 'symbols' => ['billionsNumber$subexpression$1'], 'postprocess' => $subId],
            ['name' => 'billionsNumber$subexpression$1', 'symbols' => ['billionsSimple']],
            ['name' => 'billionsNumber$subexpression$1', 'symbols' => ['billionsCompound']],
            ['name' => 'billionsNumber$subexpression$1', 'symbols' => ['millionsNumber']],
            ['name' => 'millionsNumber', 'symbols' => ['millionsNumber$subexpression$1'], 'postprocess' => $subId],
            ['name' => 'millionsNumber$subexpression$1', 'symbols' => ['millionsSimple']],
            ['name' => 'millionsNumber$subexpression$1', 'symbols' => ['millionsCompound']],
            ['name' => 'millionsNumber$subexpression$1', 'symbols' => ['thousandsNumber']],
            ['name' => 'thousandsNumber', 'symbols' => ['thousandsNumber$subexpression$1'], 'postprocess' => $subId],
            ['name' => 'thousandsNumber$subexpression$1', 'symbols' => ['thousandsSimple']],
            ['name' => 'thousandsNumber$subexpression$1', 'symbols' => ['thousandsCompound']],
            ['name' => 'thousandsNumber$subexpression$1', 'symbols' => ['hundredsNumber']],
            ['name' => 'hundredsNumber', 'symbols' => ['hundredsNumber$subexpression$1'], 'postprocess' => $subId],
            ['name' => 'hundredsNumber$subexpression$1', 'symbols' => ['hundredsSimple']],
            ['name' => 'hundredsNumber$subexpression$1', 'symbols' => ['hundredsCompound']],
            ['name' => 'hundredsNumber$subexpression$1', 'symbols' => ['tensNumber']],
            ['name' => 'tensNumber', 'symbols' => ['tensNumber$subexpression$1'], 'postprocess' => $subId],
            ['name' => 'tensNumber$subexpression$1', 'symbols' => ['teenSimple']],
            ['name' => 'tensNumber$subexpression$1', 'symbols' => ['tensSimple']],
            ['name' => 'tensNumber$subexpression$1', 'symbols' => ['tensCompound']],
            ['name' => 'tensNumber$subexpression$1', 'symbols' => ['singlesSimple']],
            ['name' => 'billionsCompound', 'symbols' => ['billionsSimple', 'space', 'billionsNumber'], 'postprocess' => $sumParts],
            ['name' => 'millionsCompound', 'symbols' => ['millionsSimple', 'space', 'millionsNumber'], 'postprocess' => $sumParts],
            ['name' => 'thousandsCompound', 'symbols' => ['thousandsSimple', 'space', 'hundredsNumber'], 'postprocess' => $sumParts],
            ['name' => 'hundredsCompound', 'symbols' => ['hundredsSimple', 'space', 'tensNumber'], 'postprocess' => $sumParts],
            ['name' => 'tensCompound', 'symbols' => ['tensSimple', 'space', 'singlesSimple'], 'postprocess' => $sumParts],
            ['name' => 'billionsSimple', 'symbols' => ['millionsNumber', 'space', 'billionsSimple$macrocall$1'], 'postprocess' => $multParts],
            ['name' => 'billionsSimple$macrocall$2', 'symbols' => ['billionsSimple$macrocall$2$string$1']],
            ['name' => 'billionsSimple$macrocall$2$string$1', 'symbols' => [['value' => "b", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "l", 'type' => Symbol::LITERAL], ['value' => "l", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'billionsSimple$macrocall$1', 'symbols' => ['billionsSimple$macrocall$2'], 'postprocess' => fn($d) => $nameMap[$d[0][0]]],
            ['name' => 'millionsSimple', 'symbols' => ['thousandsNumber', 'space', 'millionsSimple$macrocall$1'], 'postprocess' => $multParts],
            ['name' => 'millionsSimple$macrocall$2', 'symbols' => ['millionsSimple$macrocall$2$string$1']],
            ['name' => 'millionsSimple$macrocall$2$string$1', 'symbols' => [['value' => "m", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "l", 'type' => Symbol::LITERAL], ['value' => "l", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'millionsSimple$macrocall$1', 'symbols' => ['millionsSimple$macrocall$2'], 'postprocess' => fn($d) => $nameMap[$d[0][0]]],
            ['name' => 'thousandsSimple', 'symbols' => ['hundredsNumber', 'space', 'thousandsSimple$macrocall$1'], 'postprocess' => $multParts],
            ['name' => 'thousandsSimple$macrocall$2', 'symbols' => ['thousandsSimple$macrocall$2$string$1']],
            ['name' => 'thousandsSimple$macrocall$2$string$1', 'symbols' => [['value' => "t", 'type' => Symbol::LITERAL], ['value' => "h", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL], ['value' => "u", 'type' => Symbol::LITERAL], ['value' => "s", 'type' => Symbol::LITERAL], ['value' => "a", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "d", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'thousandsSimple$macrocall$1', 'symbols' => ['thousandsSimple$macrocall$2'], 'postprocess' => fn($d) => $nameMap[$d[0][0]]],
            ['name' => 'hundredsSimple', 'symbols' => ['tensNumber', 'space', 'hundredsSimple$macrocall$1'], 'postprocess' => $multParts],
            ['name' => 'hundredsSimple$macrocall$2', 'symbols' => ['hundredsSimple$macrocall$2$string$1']],
            ['name' => 'hundredsSimple$macrocall$2$string$1', 'symbols' => [['value' => "h", 'type' => Symbol::LITERAL], ['value' => "u", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "d", 'type' => Symbol::LITERAL], ['value' => "r", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "d", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'hundredsSimple$macrocall$1', 'symbols' => ['hundredsSimple$macrocall$2'], 'postprocess' => fn($d) => $nameMap[$d[0][0]]],
            ['name' => 'teenSimple', 'symbols' => ['teenSimple$subexpression$1'], 'postprocess' => $nameToVal],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$1']],
            ['name' => 'teenSimple$subexpression$1$string$1', 'symbols' => [['value' => "t", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$2']],
            ['name' => 'teenSimple$subexpression$1$string$2', 'symbols' => [['value' => "e", 'type' => Symbol::LITERAL], ['value' => "l", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "v", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$3']],
            ['name' => 'teenSimple$subexpression$1$string$3', 'symbols' => [['value' => "t", 'type' => Symbol::LITERAL], ['value' => "w", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "l", 'type' => Symbol::LITERAL], ['value' => "v", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$4']],
            ['name' => 'teenSimple$subexpression$1$string$4', 'symbols' => [['value' => "t", 'type' => Symbol::LITERAL], ['value' => "h", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "r", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$5']],
            ['name' => 'teenSimple$subexpression$1$string$5', 'symbols' => [['value' => "f", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL], ['value' => "u", 'type' => Symbol::LITERAL], ['value' => "r", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$6']],
            ['name' => 'teenSimple$subexpression$1$string$6', 'symbols' => [['value' => "f", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "f", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$7']],
            ['name' => 'teenSimple$subexpression$1$string$7', 'symbols' => [['value' => "s", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "x", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$8']],
            ['name' => 'teenSimple$subexpression$1$string$8', 'symbols' => [['value' => "s", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "v", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$9']],
            ['name' => 'teenSimple$subexpression$1$string$9', 'symbols' => [['value' => "e", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "g", 'type' => Symbol::LITERAL], ['value' => "h", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'teenSimple$subexpression$1', 'symbols' => ['teenSimple$subexpression$1$string$10']],
            ['name' => 'teenSimple$subexpression$1$string$10', 'symbols' => [['value' => "n", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'tensSimple', 'symbols' => ['tensSimple$subexpression$1'], 'postprocess' => $nameToVal],
            ['name' => 'tensSimple$subexpression$1', 'symbols' => ['tensSimple$subexpression$1$string$1']],
            ['name' => 'tensSimple$subexpression$1$string$1', 'symbols' => [['value' => "t", 'type' => Symbol::LITERAL], ['value' => "w", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "y", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'tensSimple$subexpression$1', 'symbols' => ['tensSimple$subexpression$1$string$2']],
            ['name' => 'tensSimple$subexpression$1$string$2', 'symbols' => [['value' => "t", 'type' => Symbol::LITERAL], ['value' => "h", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "r", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "y", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'tensSimple$subexpression$1', 'symbols' => ['tensSimple$subexpression$1$string$3']],
            ['name' => 'tensSimple$subexpression$1$string$3', 'symbols' => [['value' => "f", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL], ['value' => "u", 'type' => Symbol::LITERAL], ['value' => "r", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "y", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'tensSimple$subexpression$1', 'symbols' => ['tensSimple$subexpression$1$string$4']],
            ['name' => 'tensSimple$subexpression$1$string$4', 'symbols' => [['value' => "f", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "f", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "y", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'tensSimple$subexpression$1', 'symbols' => ['tensSimple$subexpression$1$string$5']],
            ['name' => 'tensSimple$subexpression$1$string$5', 'symbols' => [['value' => "s", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "x", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "y", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'tensSimple$subexpression$1', 'symbols' => ['tensSimple$subexpression$1$string$6']],
            ['name' => 'tensSimple$subexpression$1$string$6', 'symbols' => [['value' => "s", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "v", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "y", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'tensSimple$subexpression$1', 'symbols' => ['tensSimple$subexpression$1$string$7']],
            ['name' => 'tensSimple$subexpression$1$string$7', 'symbols' => [['value' => "e", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "g", 'type' => Symbol::LITERAL], ['value' => "h", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "y", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'tensSimple$subexpression$1', 'symbols' => ['tensSimple$subexpression$1$string$8']],
            ['name' => 'tensSimple$subexpression$1$string$8', 'symbols' => [['value' => "n", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL], ['value' => "y", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple', 'symbols' => ['singlesSimple$subexpression$1'], 'postprocess' => $nameToVal],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$1']],
            ['name' => 'singlesSimple$subexpression$1$string$1', 'symbols' => [['value' => "o", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$2']],
            ['name' => 'singlesSimple$subexpression$1$string$2', 'symbols' => [['value' => "t", 'type' => Symbol::LITERAL], ['value' => "w", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$3']],
            ['name' => 'singlesSimple$subexpression$1$string$3', 'symbols' => [['value' => "t", 'type' => Symbol::LITERAL], ['value' => "h", 'type' => Symbol::LITERAL], ['value' => "r", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$4']],
            ['name' => 'singlesSimple$subexpression$1$string$4', 'symbols' => [['value' => "f", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL], ['value' => "u", 'type' => Symbol::LITERAL], ['value' => "r", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$5']],
            ['name' => 'singlesSimple$subexpression$1$string$5', 'symbols' => [['value' => "f", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "v", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$6']],
            ['name' => 'singlesSimple$subexpression$1$string$6', 'symbols' => [['value' => "s", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "x", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$7']],
            ['name' => 'singlesSimple$subexpression$1$string$7', 'symbols' => [['value' => "s", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "v", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$8']],
            ['name' => 'singlesSimple$subexpression$1$string$8', 'symbols' => [['value' => "e", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "g", 'type' => Symbol::LITERAL], ['value' => "h", 'type' => Symbol::LITERAL], ['value' => "t", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'singlesSimple$subexpression$1', 'symbols' => ['singlesSimple$subexpression$1$string$9']],
            ['name' => 'singlesSimple$subexpression$1$string$9', 'symbols' => [['value' => "n", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "e", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'space', 'symbols' => [['value' => '[\s]', 'type' => Symbol::REGEX]], 'postprocess' => $nothing]
        ], 'main');
    }
}

@{%
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
%}

matchString[X] -> $X {% fn($d) => $nameMap[$d[0][0]] %}

main -> billionsNumber {% $findFirstNestedValueFromArray %}

billionsNumber -> (billionsSimple|billionsCompound|millionsNumber) {% $subId %}
millionsNumber -> (millionsSimple|millionsCompound|thousandsNumber) {% $subId %}
thousandsNumber -> (thousandsSimple|thousandsCompound|hundredsNumber) {% $subId %}
hundredsNumber -> (hundredsSimple|hundredsCompound|tensNumber) {% $subId %}
tensNumber -> (teenSimple|tensSimple|tensCompound|singlesSimple) {% $subId %}

billionsCompound -> billionsSimple space billionsNumber {% $sumParts %}
millionsCompound -> millionsSimple space millionsNumber {% $sumParts %}
thousandsCompound -> thousandsSimple space hundredsNumber {% $sumParts %}
hundredsCompound -> hundredsSimple space tensNumber {% $sumParts %}
tensCompound -> tensSimple space singlesSimple {% $sumParts %}

billionsSimple -> millionsNumber space matchString["billion"] {% $multParts %}
millionsSimple -> thousandsNumber space matchString["million"] {% $multParts %}
thousandsSimple -> hundredsNumber space matchString["thousand"] {% $multParts %}
hundredsSimple -> tensNumber space matchString["hundred"] {% $multParts %}

teenSimple -> ("ten"|"eleven"|"twelve"|"thirteen"|"fourteen"|"fifteen"|"sixteen"|"seventeen"|"eighteen"|"nineteen") {% $nameToVal %}
tensSimple -> ("twenty"|"thirty"|"fourty"|"fifty"|"sixty"|"seventy"|"eighty"|"ninety") {% $nameToVal %}
singlesSimple -> ("one"|"two"|"three"|"four"|"five"|"six"|"seven"|"eight"|"nine") {% $nameToVal %}

space -> [\s] {% $nothing %}
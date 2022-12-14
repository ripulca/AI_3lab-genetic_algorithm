<?php
$population=array();

$x0=-10; $x1=53;
$population_count=6;
$crossover_count=3;
$mutations_count=2;

function func($x){  //x[-10;53]
    return 7-45*$x-63*$x**2+$x**3;
}

function mutation($crossed, $bit_amount=6){
    $mutated=random_int(0, count($crossed)-1);
    $genetic_num=random_int(0, $bit_amount-1);
    $crossed[$mutated][$genetic_num]= $crossed[$mutated][$genetic_num] === '0' ? '1' : '0';

    return $crossed;
}

function to_binary($number, $bit_amount=6){
    $binary=decbin($number);
    if($number>=0){
    if(strlen($binary)<$bit_amount){
            $to_add=$bit_amount-strlen($binary);
            $binary=str_repeat("0", $to_add).$binary;
        }
    }else{
        $binary=substr($binary,strlen($binary)-6);
    }

    return $binary;
}

function to_dec($binary){
    foreach ($binary as &$bin) {
        $bin=bindec($bin);
    }
    return $binary;
}

function crossover($parents, $bit_amount=6){
    $tmp =array();
    for($i=0;$i<count($parents);$i++) {
        $next=$i+1;
        if($next>=count($parents)){
            $next=0;
        }
        $crossing=random_int(1, $bit_amount-1);
        $tmp[]=substr($parents[$i], 0, $crossing).substr($parents[$next], $crossing);
    }
    $parents=array_merge($parents, $tmp);
    return $parents;
}

function sort_yabs($population, $func_res){
   for($i=0;$i<count($func_res);$i++){
        for($j=0;$j<count($func_res);$j++){
            if ($i!=$j) {
                if ($func_res[$j]>$func_res[$i]) {
                    $tmp=$func_res[$i];
                    $func_res[$i]=$func_res[$j];
                    $func_res[$j]=$tmp;
                    $tmp=$population[$i];
                    $population[$i]=$population[$j];
                    $population[$j]=$tmp;
                }
            }                
        }
   }
   return $population;
};

function get_population($population, $x0=-10) {
    foreach($population as $dot) {
        echo "x= ".$dot+$x0."\n";
        echo "y= ".func($dot+$x0)."\n\n";
    }
}

function genetic($population, $population_count, $func_res1, $func_res2, $mutations_count){
    $population_best=$population;
    $population_worst=$population;

    for($i=0; $i<100; $i++) {
        if($i==0 || $i==1){
            echo "Max searching: ";
            echo "Iter ".$i."\n";
            get_population($population_best);
            echo "Min searching: ";
            echo "Iter ".$i."\n";
            get_population($population_worst);
        }

        array_multisort($func_res1, $population_best);
        array_multisort($func_res2, $population_worst);
        $population_worst=array_slice($population_worst, 0, $population_count/2);
        $population_best=array_slice($population_best, $population_count/2, $population_count);
        for($j=0;$j<$population_count/2;$j++) {
            $population_best[$j]=to_binary($population_best[$j]);
            $population_worst[$j]=to_binary($population_worst[$j]);
        }
        $population_best=crossover($population_best);
        $population_worst=crossover($population_worst);
        for($j=0;$j<$mutations_count;$j++) { 
            $population_best=mutation($population_best);
            $population_worst=mutation($population_worst);
        }
        $population_best= to_dec($population_best);
        $population_worst= to_dec($population_worst);
        for ($j=0;$j<$population_count;$j++) {
            $func_res1[$j]=func($population_best[$j]);
            $func_res2[$j]=func($population_worst[$j]);
        }
    }

    array_multisort($func_res1, $population_best);
    array_multisort($func_res2, $population_worst);
    return [
        'population_best' => $population_best,
        'population_worst' => $population_worst,
    ];
}

for ($i=0;$i<$population_count;$i++) {
    $population[$i]=random_int($x0, $x1)-$x0;
    $func_res[$i]=func($population[$i]);
}

$func_res1=$func_res;
$func_res2=$func_res;
$population=genetic($population, $population_count, $func_res1, $func_res2, $mutations_count);
echo "result: \n";
echo "min population: \n";
get_population($population['population_worst']);
echo "?????????? ?? ?????????????????????? ??????????????????????: ".$population['population_worst'][0]."   y=".func($population['population_worst'][0])."\n";
echo "max population: \n";
get_population($population['population_best']);
echo "?????????? ?? ???????????????????????? ??????????????????????: ".$population['population_best'][$population_count-1]."   y=".func($population['population_best'][$population_count-1])."\n";
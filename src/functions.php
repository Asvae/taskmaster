<?php
########################### Support functions ##############################


/**
 * Make readable text|html from array
 *
 * Example:
 * $data = [1,2,3];
 * $padding = [["(",", ",")"]];
 * echo (insertPadding($data, $padding); // (1, 2, 3)
 *
 * @param  array|string  $data      Data to wrap
 * @param  array         $padding   Html or w/e to wrap with
 *
 * @return string
 */
function insertPadding($data, $padding = [['','' /*,...*/ ] /*, ['',''], ...*/ ]) {

    // $root — inside variable, handles recursion
    $root = (func_num_args() !== 3);

    if ($root && $data === array())
        $data = '';

    if ($root)
    {
        echo ("Data: "); print_r ($data);
        echo ("Padding: "); print_r ($padding);
    }


    // Simply wrap if string passed
    if (is_string($data)){
        foreach ($padding as $wrap)
            $data = reset($wrap).$data.end($wrap);
        return $data;
    }

    // Do nothing if wrong type passed
    if (is_object($data))
        return get_class($data);
    if (is_object($data[0]))
        return get_class($data[0]);

    $depth = arrayDepth($data)-1;

    ksort($data);

    if (!isset($padding[$depth]))
        $padding[$depth] = array('','');

    $el_count = 1;
    $end = count($padding[$depth])-1;
    foreach ($data as $i => &$piece)
    {
        // Recoursion if non linear array
        if ($depth >= 1)
            $piece = insertPadding($piece, $padding, false);

        // Simply wrap if padding passed is 2 elements
        if ($end === 1) {
            $piece = $padding[$depth][0] . $piece . $padding[$depth][1];
            continue;
        }

        // Complex wrap if more than 2 elements passed.
        if ($i === 0)               #first element
            $piece = $padding[$depth][0] . $piece;
        if ($i === count($data)-1)  #last element
            $piece = $piece . $padding[$depth][$end];
        else
        { #middle elements
            if ($end !== 1){
                $piece = $piece . $padding[$depth][$el_count];
                if ($el_count < $end - 1)
                    $el_count++;
            }
        }
    }

    $data = implode($data);

    // Final wrap for root
    if ($root === true && (count($padding)-1) > $depth)
        $data = insertPadding($data, array_slice($padding,$depth), false);

    return $data;
}

function multiexplode ($delimiters,$string) {
        $tmp = explode($delimiters[0],$string);
        array_shift($delimiters);
        if($delimiters != NULL)
            foreach($tmp as $key => $val)
                $tmp[$key] = self::multiexplode($delimiters, $val);
        return  $tmp;
}

/**
 * Find array depth, recursive function
 * @param  string|array $array Output
 * @return int                 Array depth
 */
function arrayDepth($array) {
    if (!$array || !is_array($array))
        return 0;
    $array = arrayDepth($array[0]);
    return $array+1;
}

/**
 * Make readable table from array.
 *
 * @param  [type] $_array should be ["header"->["..."."..."],
 *                        "body"->[["..."."..."], ["..."."..."], ...],]
 *
 * @return [type]         [description]
 */
function array2Table ($_array)
{
    $padding_header = [array('<thead><th>','</th><th>','</th></thead>')];
    $padding_body = [array("<td>","</td>"), array('<tbody><tr>','</tr><tr>','</tr></tbody>')];

    $header = $_array["header"];
    $body = $_array["body"];

    $header = insertPadding ($header, $padding_header);
    $body = insertPadding ($body, $padding_body);

    return ("<table>". $header . $body ."</table>");
}


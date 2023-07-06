<?php

function tehran_time()
{
    date_default_timezone_set("Asia/Tehran");
    $tehran_time = time();
    $formatted_time = date("Y-m-d H:i:s", $tehran_time);

    return $formatted_time;
}

function same_date($time_stamp)
{
    $current_date = substr(tehran_time(), 0, 10);
    $compare_date = substr($time_stamp, 0, 10);
    if ($current_date != $compare_date) {
        return false;
    } else {
        return true;
    }
}


function ranking($input, $type)
{
    $last_point_array = [];
    $usernames = array_column(array_column($input, "channel"), "username");
    $point_array = array_count_values($usernames);
    arsort($point_array);

    if (file_exists("json/channel_stats_" . $type . ".json")) {
        $last_point_array = json_decode(
            file_get_contents("json/channel_stats_" . $type . ".json"),
            true
        );
        $last_rank_date = $last_point_array['date'];
        if (same_date($last_rank_date) === true){
            unlink("json/channel_stats_" . $type . ".json");
            $last_point_array['points'] = [];
        }
    } else {
        $last_point_array['points'] = [];
    }

    foreach ($point_array as $channel => $point) {
        if (!empty(array_diff_assoc($point_array, $last_point_array['points']))) {
            if (array_key_exists($channel, $last_point_array['points'])) {
                $last_point_array['points'][$channel] += $point;
            } else {
                $last_point_array['points'][$channel] = $point;
            }
        }
    }

    $final_point_array = [
        "date" => tehran_time(),
        "points" => $last_point_array['points'],
    ];

    return $final_point_array;
}

?>

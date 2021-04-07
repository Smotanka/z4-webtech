<?php
function update_table(dbController $db,$csv,$table)
{
    $header = array("﻿Full Name", "User Action", "Timestamp");
    foreach ($csv as $line) {
        if ($line != $header && count($line) == 3) {
            $attendance = new attendance();
            $attendance->setAction($line[1]);
            if (strpos($line[2], "AM")) {
                $timestamp = date('Y-m-d H:i:s', date_create_from_format('m/d/Y, H:i:s A', $line[2])->getTimestamp());

            } else {
                $timestamp = date('Y-m-d H:i:s', date_create_from_format('d/m/Y, H:i:s', $line[2])->getTimestamp());
            }
            $attendance->setTimestamp($timestamp);
            $attendance->setName($line[0]);
            $attendance_id = $db->insertAttendance($attendance, $table);
            $attendance->setId($attendance_id);
        }
    }
}

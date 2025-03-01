<?php
    /**
    * -------------------------------.*~ E G N  ~*.------------------------------
    * EGN Software: egMember
    * Copyright (c) 2012-2024 Elwin HG - http://egnsoftware.com (elwin@cvegn.com)
    * ---------------------------------------------------------------------------
    * This software is  furnished  under a  license and may  be used and   copied
    * only  in accordance with the terms of such  license and with  the inclusion
    * of  the above copyright notice.  This software or any other  copies thereof
    * may not be  provided or otherwise made available  to any other person.   No
    * title to and  ownership of the software is hereby transferred.
    * 
    * You  may  not  reverse   engineer,  decompile,  defeat  license  encryption
    * mechanisms, or  disassemble  this  software  product  or software   product
    * license. EGN  may terminate  this license if you  don't comply with any  of
    * the terms and   conditions set forth   in our  End  User  License Agreement
    * (EULA). In  such event, licensee  agrees to return licensor or  destroy all
    * copies of software upon termination  of the license. 
    * Please see the EULA file for the full End User License Agreement.
    * ---------------------------------------------------------------------------
    */
    require_once ("admin.inc.php");


    function get_income_report() {
        global $config, $timenow;
        $start_date = date('Y-m-d 00:00:00', $timenow - 3600 * 7 * 24);
        $end_date = date('Y-m-d 23:59:59', $timenow);
        $res = array();
        $q = db_result_to_array("SELECT FROM_DAYS(TO_DAYS(pay_date)) as date, count(id) as completed_count, sum(amount) as completed_amount
            FROM _table:payments WHERE pay_date BETWEEN '$start_date' AND '$end_date' AND completed > 0 GROUP BY TO_DAYS(pay_date)");
        if ($q) {
            $max_total = 0;
            foreach ($q as $k => $x) {
                $d = $x['date'];
                $res[$d] = $x;
                $total_completed += $x['completed_amount'];
                if ($x['completed_amount'] > $max_total) $max_total = $x['completed_amount'];
            }
            $res1 = array();
            for ($i = 0; $i < 7; $i++) {
                $dp = strftime($config['date_format'], $timenow - $i * 3600 * 24);
                $d = date('Y-m-d', $timenow - $i * 3600 * 24);
                $res1[$d]['date'] = $d;
                $res1[$d]['date_print'] = $dp;
                $res1[$d]['completed_count'] += number_only($res[$d]['completed_count']);
                $res1[$d]['completed_amount'] = $res[$d]['completed_amount'];

                if ($max_total) {
                    $res1[$d]['percent_v'] = round(100 * $res[$d]['completed_amount'] / $max_total);
                    $res1[$d]['percent'] = round(100 * $res[$d]['completed_amount'] / $total_completed);
                }
                if ($max_total) {
                    $x = round(100 * $res[$d]['completed_amount'] / $max_total);
                    $p = round(100 * $res[$d]['completed_amount'] / $total_completed);
                    if ($x) $res1[$d]['percent'] = "
                        <table align=left width=$x cellpadding=0 cellspacing=0 style='font-size: 5pt;'><tr><td bgcolor=red style='background-color: red;'></td></tr></table>
                        &nbsp;
                        ";
                    else  $res1[$d]['percent'] = '';
                }
            }
            ksort($res1);
            return $res1;
        }
    }
    $dbpayments = get_income_report();
    if ($dbpayments) {
        foreach ($dbpayments as $k => $x) {
            $pt[1] += $x['completed_count'];
            $pt[2] += $x['completed_amount'];
            $pt[3] += $x['admin_profit'];
            $payments .= "<tr>";
            $payments .= "<td>" . format_time(strtotime($k), 2) . "</td>";
            $payments .= "<td align=center>$x[completed_count]</td>";
            $payments .= "<td>" . format_money($x['completed_amount']) . "</td>";

            $payments .= "<td>$x[percent]</td>";
            $payments .= "</tr>";
        }
        $pt[2] = format_money($pt[2]);
        $pt[3] = format_money($pt[3]);
    }

    $q = "SELECT COUNT(IF(userlevel > '0' AND member_state = '0', userid, NULL)),
    COUNT(IF(userlevel = '0', userid, NULL)),
    COUNT(IF(member_state = '2', userid, NULL)),
    COUNT(IF(member_state = '1', userid, NULL)),
    COUNT(userid),
    SUM(balance)
    FROM _table:members";
    $member = db_result($q);

    $q = "SELECT SUM(IF(paid_status = '2', amount, NULL)),
    SUM(IF(paid_status = '1', amount, NULL)),
    COUNT(id)
    FROM _table:withdrawals";
    $wd = db_result($q);


    $articles = db_value("SELECT COUNT(id) FROM _table:articles");
    $downloads = db_value("SELECT COUNT(id) FROM _table:downloads");
    $users_online = db_value("SELECT COUNT(time) FROM _table:users_online");

    $show_array = array(
        'upcoming_events' => egReminder::get_upcoming_events($admin_data),
        'articles' => $articles, 
        'downloads' => $downloads, 
        'wd' => $wd, 
        'admin_home' => 1, 
        'pt' => $pt,
        'users_online' => $users_online,
        'total' => $total, 
        'payments' => $payments, 
        'wd' => $wd, 
        'member' => $member, 
        'dep' => $dep,
        'title' => _admin_home
    );
    egAdmin::display_admin_page("admin_home.html", $show_array);

?>
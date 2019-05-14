<?php
function id2() {
  global $pdo, $id;
  $sql = "SELECT `id2` FROM `unit`, `id_ex` WHERE `unit`.`id` = `id_ex`.`id1` AND `unit`.`id` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute([$id]);
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      return $row["id2"];
    }
  }
}
function unit_name($id) {
  global $pdo;
  $sql = "SELECT `unit_name_{$_COOKIE["l"]}` FROM `unit`, `id_ex` WHERE `unit`.`id` = `id_ex`.`id1` AND `unit`.`id` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute([$id]);
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      return $row["unit_name_{$_COOKIE["l"]}"];
    }
  }
}
function origin() {
  global $pdo, $id;
  $sql = "SELECT `unit`.`origin`, `name_{$_COOKIE["l"]}` FROM `unit`, `origin` WHERE `unit`.`origin` = `origin`.`origin` AND `unit`.`id` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute([$id]);
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      return "<a href='search_v2?origin={$row["origin"]}'>{$row["name_".$_COOKIE["l"]]}</a>";
    }
  }
}
function unit_info_helper($a, $b, $c) {
  return "
    <tr>
      <td>{$a}</td>
      <td".($c<0?" colspan='2'":"").">{$b}</td>
      ".($c>0?"<td>{$c}</td>":"")."
    </tr>
";
}
function unit_info() {
  global $pdo, $id;
  $sql = "SELECT `hp`, `rank`, `pos`, `atk`, `def`, `spd`, `ctl`, `ratk`, `rdef`, `rspd`, `rctl`, `lock`, `rlock` FROM `unit` WHERE `unit`.`id` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute([$id]);
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      return "
    <tr>
      <td>ID: ".$id."</td>
      <td>{$row["rank"]}</td>
      <td><a href='search_v2?pos={$row["pos"]}'><img srcc='{$row["pos"]}' class='pos'></a></td>
    </tr>
    <tr>
      <td colspan='3'><img srcc='".$id."' class='unit' tit='".unit_name($id)."' alt='".unit_name($id)."' /></td>
    </tr>
    <tr>
      <td>".tos("血量", "血量")."</td>
      <td colspan='2'>{$row["hp"]}</td>
    </tr>".
    unit_info_helper(tos("攻擊", "攻击"),$row["atk"],$row["ratk"]).
    unit_info_helper(tos("防禦", "防御"),$row["def"],$row["rdef"]).
    unit_info_helper(tos("速度", "速度"),$row["spd"],$row["rspd"]).
    unit_info_helper(tos("操控", "操控"),$row["ctl"],$row["rctl"]).
    unit_info_helper(tos("總和", "总和"),$row["atk"]+$row["def"]+$row["spd"]+$row["ctl"],$row["ratk"]+$row["rdef"]+$row["rspd"]+$row["rctl"]).
    unit_info_helper(tos("鎖敵距離", "锁敌距离"),$row["lock"],$row["rlock"]);
    }
  }
}
function in_cm() {
  global $pdo, $id;
  $sql = "SELECT GROUP_CONCAT(machine ORDER BY machine SEPARATOR '<br>') AS in_cm FROM `capsule` WHERE a = ? OR b = ? OR c = ? OR d = ? OR e = ? OR f = ?;";
  $result = $pdo->prepare($sql);
  $result->execute(array_fill(0, 6, $id));
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      if ($row["in_cm"] != "") {
        $temp = explode("<br>", $row["in_cm"]);
        $in_cm = "";
        for ($x = 0; $x < sizeof($temp); $x++) {
          $in_cm .= "<a href='search_v2?machine={$temp[$x]}'>[{$temp[$x]}]</a>　";
        }
        return "<tr><td>".tos("扭蛋機", "扭蛋机")."</td><td colspan='2'>{$in_cm}</td></tr>";
      }
    }
  }
}
function tags() {
  global $pdo, $id;
  $html = "";
  $sql = [
    "SELECT `tag1` AS tag FROM `unit` WHERE `id` = $id;",
    "SELECT `tag3` AS tag FROM `unit` WHERE `id` = $id;",
    "SELECT `tag4` AS tag FROM `unit` WHERE `id` = $id;"
  ];
  $text = [["大型", "裝甲解除", "技能激活"],["大型", "装甲解除", "技能激活"]];
  for ($i = 0; $i < count($sql); $i++) {
    $result = $pdo->prepare($sql[$i]);
    $result->execute();
    if ($result->rowCount() == 1) {
      while ($row = $result->fetch()) {
        if ($row["tag"] == "1") {
          if ($html == "") {
            $html .= "<tr><td colspan='3'>";
          }
          $html .= "［".tos($text[0][$i], $text[1][$i])."］";
        }
      }
    }
  }
  return $html;
}
function skl_sp() {
  global $pdo, $id;
  $sql = "SELECT `unit`.`skl1`, (SELECT CONCAT(`name_{$_COOKIE["l"]}`, '\n\n', `desc_{$_COOKIE["l"]}`) FROM `skill` WHERE `unit`.`skl1` = `skl`) AS skl1_desc,
                 `unit`.`skl2`, (SELECT CONCAT(`name_{$_COOKIE["l"]}`, '\n\n', `desc_{$_COOKIE["l"]}`) FROM `skill` WHERE `unit`.`skl2` = `skl`) AS skl2_desc,
                 `unit`.`sp1`, CONCAT((SELECT `name_{$_COOKIE["l"]}` FROM `skill` WHERE `unit`.`sp1` = `skl`), '\n\n', '".tos("傷害", "伤害")."：', `unit`.`sp1dmg`) AS sp1_desc,
                 `unit`.`sp2`, CONCAT((SELECT `name_{$_COOKIE["l"]}` FROM `skill` WHERE `unit`.`sp2` = `skl`), '\n\n', '".tos("傷害", "伤害")."：', `unit`.`sp2dmg`) AS sp2_desc
          FROM `unit` WHERE `id` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute([$id]);
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      return "
  <table id='skl_sp' class='mobile hide'>
    <tr style='font-weight: bold;'>
      <td>".tos("技能一", "技能一")."</td>
      <td>".tos("技能二", "技能二")."</td>
      <td>".tos("必殺一", "必杀一")."</td>".
      ($row["sp2"] != '' ? "<td>".tos("必殺二", "必杀二")."</td>" : "")."
    </tr>
    <tr>
      <td><a href='search_v2?skl={$row["skl1"]}'><img class='skill' srcc='{$row["skl1"]}' tit='{$row["skl1_desc"]}' alt='{$row["skl1_desc"]}' /></a></td>
      <td><a href='search_v2?skl={$row["skl2"]}'><img class='skill' srcc='{$row["skl2"]}' tit='{$row["skl2_desc"]}' alt='{$row["skl2_desc"]}' /></a></td>
      <td><a href='search_v2?sp={$row["sp1"]}'><img class='sp' srcc='{$row["sp1"]}' tit='{$row["sp1_desc"]}' alt='{$row["sp1_desc"]}' /></a></td>".
      ($row["sp2"] != '' ? "<td><a href='search_v2?sp={$row["sp2"]}'><img class='sp' srcc='{$row["sp2"]}' tit='{$row["sp2_desc"]}' alt='{$row["sp2_desc"]}' /></a></td>" : "")."
    </tr>
  </table>
  <table id='skl_sp' class='mobile'>
    <tr>
      <td style='font-weight: bold;'>".tos("技能一", "技能一")."</td>
      <td><a href='search_v2?skl={$row["skl1"]}'><img class='skill' srcc='{$row["skl1"]}' tit='{$row["skl1_desc"]}' alt='{$row["skl1_desc"]}' /></a></td>
    </tr>
    <tr>
      <td style='font-weight: bold;'>".tos("技能二", "技能二")."</td>
      <td><a href='search_v2?skl={$row["skl2"]}'><img class='skill' srcc='{$row["skl2"]}' tit='{$row["skl2_desc"]}' alt='{$row["skl2_desc"]}' /></a></td>
    </tr>
    <tr>
      <td style='font-weight: bold;'>".tos("必殺一", "必杀一")."</td>
      <td><a href='search_v2?sp={$row["sp1"]}'><img class='sp' srcc='{$row["sp1"]}' tit='{$row["sp1_desc"]}' alt='{$row["sp1_desc"]}' /></a></td>
    </tr>".
      ($row["sp2"] != '' ? "<tr><td style='font-weight: bold;'>".tos("必殺二", "必杀二")."</td><td><a href='search_v2?sp={$row["sp2"]}'><img class='sp' srcc='{$row["sp2"]}' tit='{$row["sp2_desc"]}' alt='{$row["sp2_desc"]}' /></a></td></tr>" : "")."
  </table>
";
    }
  }
}
function wpn($no) {
  global $pdo, $id;
  $html = "";
  $s2 = 0;
  $sql = "SELECT `tag0`, `wpn`, `rng`, `dmg`, `sets`, `cd` 
  FROM `unit`, `weapon` 
    LEFT JOIN `weapon_tag` 
      ON `weapon`.`id` = `weapon_tag`.`id`
        AND `weapon`.`no` = `weapon_tag`.`no`
  WHERE `weapon`.`id` = `unit`.`id` 
    AND `weapon`.`id` = ? AND `weapon`.`no` = ?
  GROUP BY `weapon`.`no`;";
  $result = $pdo->prepare($sql);
  $result->execute([$id, $no]);
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      $html .= "
    <tr".($row["tag0"] == "1" && $row["wpn"] == "32" ? " bgcolor='#DDDDFF'" : "").">
      <td>".($row["wpn"]==0||$row["wpn"]==999?"":"<a href='search_v2?wpn={$row["wpn"]}'>")."<img srcc='{$row["wpn"]}' class='weapon'>".($row["wpn"]==0||$row["wpn"]==999?"":"</a>")."</td>
      <td class='mobile hide'>{$row["rng"]}</td>
      <td>{$row["dmg"]}".(($no == 8 || $no == 9) ? "%" : "")."</td>
      <td class='mobile hide'>".(($no == 8 || $no == 9) ? "" : $row["sets"])."</td>
      <td class='mobile hide'>{$row["cd"]}</td>
      <td>";
      if (($no == 8 || $no == 9) && $row["sets"] == "1") {
        $s2 = 1;
      }
    }
  }
  else {
    $html .= "
    <tr>
      <td><img srcc='999' class='weapon'></td>
      <td class='mobile hide'>-</td>
      <td>-</td>
      <td class='mobile hide'></td>
      <td class='mobile hide'></td>
      <td>";
  }
  if ($s2 == 1) {
    $html .= tos("光束盾", "光束盾");
  }
  else {
    $sql = "SELECT GROUP_CONCAT(`tag_{$_COOKIE["l"]}` ORDER BY LENGTH(`tag_{$_COOKIE["l"]}`) SEPARATOR '<br>') AS tag 
    FROM `tag`, `weapon_tag`
    WHERE `tag`.`id` = `weapon_tag`.`tag`
      AND `weapon_tag`.`id` = ? AND `weapon_tag`.`no` = ? 
    GROUP BY `weapon_tag`.`id`, `weapon_tag`.`no`;";
    $result = $pdo->prepare($sql);
    $result->execute([$id, $no]);
    if ($result->rowCount() == 1) {
      while ($row = $result->fetch()) {
        $html .= (($no == 8 || $no == 9) ? "" : $row["tag"]);
      }
    }
  }
  $html .= "</td>
    </tr>
";
  return preg_replace("/-1(\.00)?/", "?", $html);
}
function ma_ca($no, $tag4) {
  global $pdo, $id;
  $hex = strtoupper(dechex(intval($id)));
  if ($tag4 == "") {
    $hex = $hex[2].$hex[3]." ".$hex[0].$hex[1];
  }
  else {
    $hex = strtoupper(dechex(hexdec($hex[2].$hex[3])+1))." ".$hex[0].$hex[1];
  }
  $ma = "ma";
  $ca = "ca";
  if ($no == 1) {
    $ma = "r{$ma}";
    $ca = "r{$ca}";
  }
  $sql = "SELECT `$ma`, `$ca` FROM `unit` WHERE `id` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute([$id]);
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      return "
<tr>
<td colspan='3'>".tos("型態代碼：", "型态代码：")."<input type='text' value='{$hex}' size='5' /></td>
<td colspan='3' class='mobile hide'>".($row[$ma] == "1" || $row[$ca] == "1"?tos("型態特性：", "型态特性："):"").
($row[$ma] == "1" ? "MA" : "").
($row[$ma] == "1" && $row[$ca] == "1" ? "、" : "").
($row[$ca] == "1" ? tos("格鬥反擊", "格斗反击") : "").
"</td>".
($row[$ma] == "1" || $row[$ca] == "1"?
"</tr>
<tr class='mobile'>
<td colspan='3'>".tos("型態特性：", "型态特性：").
($row[$ma] == "1" ? "MA" : "").
($row[$ma] == "1" && $row[$ca] == "1" ? "、" : "").
($row[$ca] == "1" ? tos("格鬥反擊", "格斗反击") : "").
"</td>
</tr>
"
:
"");
    }
  }
}
function blueprint() {
  global $pdo, $id;
  $sql = "SELECT * FROM `blueprint` WHERE `id` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute([$id]);
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      return "
<br>
<table id='blueprint' class='mobile hide'>
  <tr style='font-weight: bold;'>
    <td colspan='5'>".tos("設計圖", "设计图")."</td>
  </tr>
  <tr>
    <td bgcolor='#DDDDFF'>
      <a href='search_v2?id={$row["a"]}'><img srcc='{$row["a"]}' class='unit' tit='".unit_name($row["a"])."' alt='".unit_name($row["a"])."'></a>
    </td>".
    ($row["b"] != "" ? "<td><a href='search_v2?id={$row["b"]}'><img srcc='{$row["b"]}' class='unit' tit='".unit_name($row["b"])."' alt='".unit_name($row["b"])."'></a></td>" : "").
    ($row["c"] != "" ? "<td><a href='search_v2?id={$row["c"]}'><img srcc='{$row["c"]}' class='unit' tit='".unit_name($row["c"])."' alt='".unit_name($row["c"])."'></a></td>" : "").
    ($row["d"] != "" ? "<td><a href='search_v2?id={$row["d"]}'><img srcc='{$row["d"]}' class='unit' tit='".unit_name($row["d"])."' alt='".unit_name($row["d"])."'></a></td>" : "").
    ($row["e"] != "" ? "<td><a href='search_v2?id={$row["e"]}'><img srcc='{$row["e"]}' class='unit' tit='".unit_name($row["e"])."' alt='".unit_name($row["e"])."'></a></td>" : "")."
  </tr>
  <tr>
    <td bgcolor='#DDDDFF'>Lv {$row["aa"]}</td>".
    ($row["b"] != "" ? "<td>Lv {$row["bb"]}</td>" : "").
    ($row["c"] != "" ? "<td>Lv {$row["cc"]}</td>" : "").
    ($row["d"] != "" ? "<td>Lv {$row["dd"]}</td>" : "").
    ($row["e"] != "" ? "<td>Lv {$row["ee"]}</td>" : "")."
  </tr>
</table>
<table id='blueprint' class='mobile'>
  <tr style='font-weight: bold;'>
    <td colspan='2'>".tos("設計圖", "设计图")."</td>
  </tr>
  <tr>
    <td bgcolor='#DDDDFF'>
      <a href='search_v2?id={$row["a"]}'><img srcc='{$row["a"]}' class='unit' tit='".unit_name($row["a"])."' alt='".unit_name($row["a"])."'></a>
    </td>
    <td bgcolor='#DDDDFF'>Lv {$row["aa"]}</td>
  </tr>".
    ($row["b"] != "" ? "<tr><td><a href='search_v2?id={$row["b"]}'><img srcc='{$row["b"]}' class='unit' tit='".unit_name($row["b"])."' alt='".unit_name($row["b"])."'></a></td><td>Lv {$row["bb"]}</td></tr>" : "").
    ($row["c"] != "" ? "<tr><td><a href='search_v2?id={$row["c"]}'><img srcc='{$row["c"]}' class='unit' tit='".unit_name($row["c"])."' alt='".unit_name($row["c"])."'></a></td><td>Lv {$row["cc"]}</td></tr>" : "").
    ($row["d"] != "" ? "<tr><td><a href='search_v2?id={$row["d"]}'><img srcc='{$row["d"]}' class='unit' tit='".unit_name($row["d"])."' alt='".unit_name($row["d"])."'></a></td><td>Lv {$row["dd"]}</td></tr>" : "").
    ($row["e"] != "" ? "<tr><td><a href='search_v2?id={$row["e"]}'><img srcc='{$row["e"]}' class='unit' tit='".unit_name($row["e"])."' alt='".unit_name($row["e"])."'></a></td><td>Lv {$row["ee"]}</td></tr>" : "")."
  </tr>
</table>
";
    }
  }
}
function material() {
  global $pdo, $id;
  $parents = [];
  $is_key = [];
  $sql = "SELECT GROUP_CONCAT(`id` ORDER BY `id` SEPARATOR '<br>') AS material FROM `blueprint` WHERE `a` = ? OR `b` = ? OR `c` = ? OR `d` = ? OR `e` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute(array_fill(0, 5, $id));
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      if ($row["material"] != null) {
        $parents = explode("<br>", $row["material"]);
      }
      else {
        return;
      }
    }
  }
  $sql = "SELECT GROUP_CONCAT(`a` ORDER BY `id` SEPARATOR '<br>') AS material FROM `blueprint` WHERE `a` = ? OR `b` = ? OR `c` = ? OR `d` = ? OR `e` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute(array_fill(0, 5, $id));
  if ($result->rowCount() == 1) {
    while ($row = $result->fetch()) {
      if ($row["material"] != null) {
        $is_key = explode("<br>", $row["material"]);
      }
      else {
        return;
      }
    }
  }
  $row = floor(sqrt(sizeof($parents)));
  $column = ceil(sizeof($parents)/$row);
  $index = 0;
  $html = "<br><table id='material' class='mobile hide'><tr style='font-weight: bold;'><td colspan='5'>材料</td></tr>";
  for ($i = 0; $i < $row; $i++) {
    $html .= "<tr>";
    for ($j = 0; $j < $column; $j++) {
      $html .= "<td".($is_key[$index] == $id ? " bgcolor='#DDDDFF'" : "").">";
      if ($parents[$index] != "") {
        $html .= "<a href='search_v2?id={$parents[$index]}'><img srcc='{$parents[$index]}' class='unit' tit='".unit_name($parents[$index])."' alt='".unit_name($parents[$index])."' /></a>";
      }
      $index++;
      $html .= "</td>";
    }
    $html .= "</tr>";
  }
  $html .= "</table>";
  $row = ceil(sqrt(sizeof($parents)));
  $column = floor(sizeof($parents)/$row);
  $index = 0;
  $html .= "<table id='material' class='mobile'><tr style='font-weight: bold;'><td colspan='5'>材料</td></tr>";
  for ($i = 0; $i < $row; $i++) {
    $html .= "<tr>";
    for ($j = 0; $j < $column; $j++) {
      $html .= "<td".($is_key[$index] == $id ? " bgcolor='#DDDDFF'" : "").">";
      if ($parents[$index] != "") {
        $html .= "<a href='search_v2?id={$parents[$index]}'><img srcc='{$parents[$index]}' class='unit' tit='".unit_name($parents[$index])."' alt='".unit_name($parents[$index])."' /></a>";
      }
      $index++;
      $html .= "</td>";
    }
    $html .= "</tr>";
  }
  $html .= "</table>";
  return $html;
}
if ($_GET["id"] == "random") {
  $sql = "SELECT `id` FROM `unit` ORDER BY RAND() LIMIT 1;";
  $result = $pdo->prepare($sql);
  $result->execute();
  $id = $result->fetch()["id"];
  header("Location: search_v2?id=$id");
}
else {
  $id = intval($_GET["id"]);
}
$title = unit_name($id)." - ";
if ($id != null) {
  $sql = "SELECT `tag0`, `tag4`, `sp2` FROM `unit` WHERE `id` = ?;";
  $result = $pdo->prepare($sql);
  $result->execute([$id]);
  if ($result->rowCount() == 1) {
    while($row = $result->fetch()) {
      $query_html = "
<div id='id'>
  <br>
  <table>
    <tr>
      <td><a href='http://cfo.tiraura.jp/unit_detail.php?id={$id}' target='_blank' text='CFO'></a></td>
      <td><a href='https://www.olgame.tw/sds/robot_detail.php?id={$id}' target='_blank' text='OLG'></a></td>
      <td><a href='https://sdplayer.club/detail.html?id={$id}' target='_blank' text='SDP'></a></td>
      <td><a href='https://www.yukict.com/bbs/viewthread.php?tid=".id2()."' target='_blank' text='YUKI'></a></td>
    </tr>
  </table>
  <br>
  <table id='info'>
    <tr>
      <td colspan='3' style='font-weight: bold;'>".unit_name($id)."</td>
    </tr>
    <tr>
      <td colspan='3'>".origin()."</td>
    </tr>".
    unit_info().
    in_cm().
    tags()."
  </table>
  <br>
  ".
  skl_sp()."
  <br>
  <table id='armament'>
    <tr style='font-weight: bold;'>
      <td>".tos("武器", "武器")."</td>
      <td class='mobile hide'>".tos("射程距離", "射程距离")."</td>
      <td>".tos("攻擊傷害", "攻击伤害")."</td>
      <td class='mobile hide'>".tos("彈藥數量", "弹药数量")."</td>
      <td class='mobile hide'>".tos("裝填時間", "装填时间")."</td>
      <td>".tos("武器效果", "武器效果")."</td>
    </tr>".
    wpn(0).
    wpn(1).
    wpn(2).
    wpn(8).
    ma_ca(0, "");
      if (($row["sp2"] != '')) {
        $query_html .= "
        <tr>
          <td colspan='8'></td>
        </tr>".
        wpn(3).
        wpn(4).
        wpn(5).
        wpn(9).
        ma_ca(1, $row["tag4"]);
      }
      $query_html .= "</table>";
      $query_html .= blueprint();
      $query_html .= material()."</div><script>$('div#wrapper').addClass('mobile hide');</script>";
    }
  } else {
    header("Location: search_v2");
  }
} else {
  header("Location: search_v2");
}
?>
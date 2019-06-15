<?php
function get_news() {
  $html = "";
  $news = explode(";", minify(_require("inc/home/rss.php")));
  if ($news[0] == "") {
    return "<div class='item'><p>沒有</p></div>";
  }
  for ($x = 0; $x < 5; $x++) {
    if ($news[$x] != "") {
      $news[$x] = explode("::", $news[$x]);
      $news[$x][1] = explode("||", $news[$x][1]);
      $html .= "<div class='item'><b>{$news[$x][0]}</b> <ul>";
      for ($y = 0; $y < count($news[$x][1]); $y++) {
        $html .= "<li>{$news[$x][1][$y]}</li>";
      }
      $html .= "</ul></div>";
    }
  }
  if (count($news) >= 5) {
    $new = $news[count($news)-2];
    $new = explode("::", $new);
    $new[1] = explode("||", $new[1]);
    $html .= "<div class='item'><b>{$new[0]}</b> <ul>";
    for ($y = 0; $y < count($new[1]); $y++) {
      $html .= "<li>{$new[1][$y]}</li>";
    }
    $html .= "</ul></div>";
  }
  return preg_replace("/([0-9]{5})/", "<img class='unit' srcc='$1' tit='' style='width:1em;height:1em;' />", $html);
}
?>
<table id="news">
  <tr>
    <td><?=tos("站內公告","站内公告")?> 📰</td>
  </tr>
  <tr>
    <td><?=get_news()?></td>
  </tr>
</table>
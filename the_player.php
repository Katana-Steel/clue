<?php 
require_once("clue.php");
session_start();
function options(&$ary, $t="")
{
    foreach( $ary as $opt ) {
        $sel = "";
        if($opt == $t) $sel = " selected";
        echo "<option value='${opt}'${sel}>${opt}</option>\n";
    }
}
if(isset($_SESSION["player"])) {
$player = $_SESSION["player"];
?>
<script type="text/javascript">
    function sendTurn() {
        var action = "take_turn.php?";
        var ez = document.getElementById('tt').elements;
        for(i=0;i<ez.length;i++) {
            if(ez[i].type == 'button') continue;
            if(ez[i].type == 'radio' && ! ez[i].checked) continue;
            action += ez[i].name + "=" + ez[i].value;
            if(i != (ez.length-1)) action += "&";
        }
        ajax_loadContent('out', action);
        // window.setTimeout('ajax_loadContent("player", "the_player.php");', 500);
    }
</script>
<form id="tt">
  <table>
  <tr>
  <td align="center"><input type="radio" name="turn" value="g" /> Guess <input type="radio" name="turn" value="t" checked /> Try Theory</td>
  </tr>
  <tr>
  <td align="center"><select name="per"><?php options($_SESSION["game"]->persons, $player->picks[0]); ?></select> <select name="wea"><?php options($_SESSION["game"]->weapons,$player->picks[1]); ?></select> <select name="pla"><?php options($_SESSION["game"]->places, $player->picks[2]); ?></select></td>
  </tr>
  <tr>
  <td align="center"><input type="button" value="send" onClick="sendTurn();" /></td>
  </tr>
  </table>
  </form>
  <?php 
    echo $player->name . " turn(" . $player->turns .")";
  ?>
   <table>
    <tr><?php
        foreach($player->cards as $card)
            echo "<td style=\"border: 1px solid black;\">${card}</td>";
    ?></tr>
    <tr><?php
        foreach($player->shown as $card)
            echo "<td style=\"border: 1px solid red;\">${card}</td>";
    ?></tr>
   </table>
<?php 
} else {
    echo "Sorry it seems like the Game is over, Refresh the page for a new game ^_^";
}
?>

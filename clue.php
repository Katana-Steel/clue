<?php
/* the game of Clue, in PHP by Rene Kjellerup */

class Player {
    var $name;
    var $type;
    var $cards;
    var $shown; // for the ai, to help select the card to show the player
                // and to store card shown to the player from the ai
    var $status; // is the player alive?
    
    var $picks;
    var $turns;
    
    function Player($n, $ai=true)
    {
        $this->name = $n;
        $this->type = $ai;
        $this->cards = array();
        $this->shown = array();
        $this->picks = array();
        $this->turns = 0;
        $this->status = true;
    }
    
    function addCard($c) {
        $this->cards[] = $c;
        sort($this->cards);
    }
    
    function setPicks($pers, $weap, $plac)
    {
        $this->picks = array($pers, $weap, $plac);
    }
    
    function store($card) 
    {
        if($this->type)
            return;
        if(!in_array($card[1], $this->shown))
            $this->shown[] = $card[1];
        sort($this->shown);
    }

    function test($pers, $weap, $plac)
    {
        $ret = NULL;
        $choice = array();
        if(in_array($pers,$this->cards)) 
            $choice[] = $pers;
        if(in_array($weap,$this->cards)) 
            $choice[] = $weap;
        if(in_array($plac,$this->cards)) 
            $choice[] = $plac;
        
        if(count($choice) > 0) {
            $shw = array();
            foreach( $choice as $c ) {
                if(in_array($c, $this->shown))
                    $shw[] = $c;
            }
            if(count($shw) > 0) {
                shuffle($shw);
                $ret = array_pop($shw);
            } else {
                shuffle($choice);
                $ret = array_pop($choice);
                $this->shown[] = $ret;
                sort($this->shown);
            }
        }
        return $ret;
    }
};

class Clue {
    var $the_deed;
    var $players;
    var $turns;
    // the categories;
    var $persons;
    var $weapons;
    var $places;
    
    function Clue($n, $type="solo") // currently only setup for solo play, will have a "light" db backend for multiplayer
    {   // thinking about staring a session here and store the client's player there, however it is planned to have this stored there as well ....
        $this->players = array();
        $this->turns = 0;
        $this->players[] = new Player("You", false);
        for($i=1; $i<$n; $i++)
            $this->players[] = new Player("Ai${i}");
        
        $this->persons = array("Orange", "Blue", "Green", "White", "Red", "Black");
        $this->weapons = array("Knife", "Rope", "Hammer", "Candlestick", "Gun", "Golfclub", "Wrench", "Poison");
        $this->places  = array("Basement", "Lobby", "Poolhall", "Library", "Kitchen", "Livening Room", "Greenhouse");
        sort($this->persons);
        sort($this->weapons);
        sort($this->places);

        $this->the_deed = array("person" => '', "weapon" => '', "place" => '');
        $cur = $this->persons;
        shuffle($cur);
        $this->the_deed["person"] = array_pop($cur);
        $stack = $cur;
        
        $cur = $this->weapons;
        shuffle($cur);
        $this->the_deed["weapon"] = array_pop($cur);
        $stack = array_merge($stack, $cur);
        
        $cur = $this->places;
        shuffle($cur);
        $this->the_deed["place"] = array_pop($cur);
        $stack = array_merge($stack, $cur);
        
        $pl = count($this->players);
        for($i=0; count($stack) > 0; $i++) {
            shuffle($stack);
            $this->players[$i%$pl]->addCard(array_pop($stack));
        }
    }
    
    function tryTheory($p, $pers, $weap, $plac)
    {
        $dis_proof = array();
        foreach($this->players as $play) {
            if($play == $p) continue;
            $card = $play->test($pers,$weap,$plac);
            if($card != NULL) {
                $dis_proof[] = $play->name;
                $dis_proof[] = $card;
                break;
            }
        }
        return $dis_proof;
    }

    function guess($p, $pers, $weap, $plac)
    {
        $proof = $this->the_deed;
        
        if($proof["person"] == $pers)
            if($proof["weapon"] == $weap)
                if($proof["place"] == $plac) {
                    // $this->end();
                    return array(); // should freeze all other players too and declare the winner.
                }
        
        $p->status = false;
        return $proof;
    }
    
    // this needs modification for multiplayer
    function getCurPlayer() {
        return $this->players[0];
    }
    
    function takeTurn($type) {
        $player = $this->getCurPlayer();
        $player->turns++;
        switch($type) {
        case "t":
            return $this->tryTheory($player,$player->picks[0],$player->picks[1],$player->picks[2]);
            break;
        case "g":
            return $this->guess($player,$player->picks[0],$player->picks[1],$player->picks[2]);
            break;
        }
    }
};

?>

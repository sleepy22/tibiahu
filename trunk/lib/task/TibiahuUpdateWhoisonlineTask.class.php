<?php

class TibiahuUpdateWhoisonlineTask extends sfBaseTask
{

  protected function configure()
  {
    $this->namespace = "tibiahu";
    $this->name = "update-whoisonline";
    $this->briefDescription = "Frissit a whoisonline lista alapjan";
    
    $this->detailedDescription = <<<EOF
A [tibiahu:update-whoisonline|INFO] task a tibia.com whoisonline
listaja alapjan frissiti a karakterek szintjet, kasztjat, valamint
a legutobb latott idopontjukat. Szintlepeskor az is naplozasra kerul,
halalnal az ok automatikusan ki lesz toltve es az idopont pontositva.
EOF;
  }
  
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    $servers = ServerPeer::getAllEnabled();
    $updates = $inserts = $levelups = $leveldowns = 0;    
    
    foreach ($servers as $server) {
      echo("Whoisonline lista lekerese ({$server->getName()})...\n");
      $characters = TibiaWebsite::whoIsOnline($server->getName());
      
      foreach ($characters as $character) {
        set_time_limit(5);
        
        //echo("{$character["name"]}\t");
        $char = CharacterPeer::retrieveByName($character["name"]);
        if (!$char) {
          $char = new Character();
          $char->setName($character["name"]);
          $char->setServerId($server->getId());
          ++$inserts;
          $this->logSection("new char", $character["name"]);
          //echo("new character\t");
        } else {
          ++$updates;
        }
        
        $char->setVocation($character["vocation"]);
        if (!$char->isNew() && ($character["level"] != $char->getLevel())) {
          //echo("level changed from {$char->getLevel()} to {$character["level"]}\t");
          $lh = new LevelHistory();
          $lh->setCharacter($char);
          $lh->setLevel($character["level"]);
          if ($character["level"] < $char->getLevel()) {
            if (count($d = TibiaWebsite::lastDeath($character["name"]))) {
              $lh->setCreatedAt($d["time"]);
              $lh->setReason($d["reason"]);
            }
            //echo("lvldown\t");
            $this->logSection("lvldown", $character["name"]);
            ++$leveldowns;
          } else {
            $this->logSection("lvlup", $character["name"]);
            ++$levelups;
          }
        }
        $char->setLevel($character["level"]);
        $char->setLastSeen(time());
        try {
          $char->save();
        }
        catch (Exception $e) {
          echo("MENTESI HIBA\n" . $e->getMessage() . "\n");
        }
        
        //echo("\n");
      }    
    }
          
    $cronlog = new CronLog();
    $cronlog->setType("whoisonline");
    $cronlog->setData(serialize(array(
      "updates"    => $updates,
      "inserts"    => $inserts,
      "levelups"   => $levelups,
      "leveldowns" => $leveldowns
    )));
    $cronlog->save();
    
    $this->logSection('update', sprintf("%s karakter frissitve", count($characters)));
    $this->logSection("update", "{$levelups} lvlup, {$leveldowns} lvldown");
  }

}
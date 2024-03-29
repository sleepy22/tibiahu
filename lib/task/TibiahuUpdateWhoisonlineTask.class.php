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
    
    foreach ($servers as $server) { /** @var Server $server */
      echo("Whoisonline lista lekerese ({$server->getName()})...\n");
      $characters = TibiaWebsite::whoIsOnline($server->getName());
      
      $updatedata[$server->getName()] = array(
        "updates" =>      0,
        "inserts" =>      0,
        "levelups"  =>    0,
        "leveldowns"  =>  0,
      );
      
      foreach ($characters as $character) {
        set_time_limit(10);
        
        //echo("{$character["name"]}\t");
        $char = CharacterPeer::retrieveByName($character["name"]);
        if (!$char) {
          $char = new Character();
          $char->setName($character["name"]);
          $char->setServerId($server->getId());
          ++$updatedata[$server->getName()]["inserts"];
          $this->logSection("new char", $character["name"]);
          //echo("new character\t");
        } else {
          ++$updatedata[$server->getName()]["updates"];
        }
        
        if (!$char->isNew() && $char->getVocation() != $character["vocation"]) {
          //vocation has changed
          $char->setVocation($character["vocation"]);
        }
        
        if (!$char->isNew() && $char->getServerId() != $server->getId()) {
          //server has changed
          $char->setServer($server);
        }
        
        if (!$char->isNew() && ($character["level"] != $char->getLevel())) {
          //echo("level changed from {$char->getLevel()} to {$character["level"]}\t");
          if ($character["level"] < $char->getLevel()) {
            if (count($d = TibiaWebsite::lastDeath($character["name"]))) {
              $c = new Criteria();
              $c->add(LevelHistoryPeer::CHARACTER_ID, $char->getId());
              $c->add(LevelHistoryPeer::CREATED_AT, $d["time"]);

              if ($lh = LevelHistoryPeer::doSelectOne($c)) {
                $lh->setLevel($character["level"]);
              } else {
                $lh = new LevelHistory();
                $lh->setCharacter($char);
                $lh->setLevel($character["level"]);
                $lh->setCreatedAt($d["time"]);
                $lh->setReason($d["reason"]);
              }
              
              $lh->save();
              
            }
            //echo("lvldown\t");
            $this->logSection("lvldown", $character["name"]);
            ++$updatedata[$server->getName()]["leveldowns"];
          } else {
            $lh = new LevelHistory();
            $lh->setCharacter($char);
            $lh->setLevel($character["level"]);
            $this->logSection("lvlup", $character["name"]);
            ++$updatedata[$server->getName()]["levelups"];
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
      } // /foreach
      $chars_seen += count($characters);      
    }
    
    print_r($updatedata);
          
    $cronlog = new CronLog();
    $cronlog->setType("whoisonline");
    $cronlog->setData(serialize($updatedata));
    $cronlog->save();
    
    $this->logSection('update', sprintf("%s karakter frissitve", $chars_seen));
  }

}

<?php

declare(strict_types = 1);

namespace folderpluginloader;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\PluginDescription;
use pocketmine\Server;
use function file_exists;
use function file_get_contents;
use function is_dir;
use function rmdir;

final class FolderPluginLoader extends PluginBase{

    protected function onLoad() : void{
        rmdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerInterface(new class implements PluginLoader{

            public function canLoadPlugin(string $path) : bool{
                return is_dir($path) && file_exists("$path/plugin.yml") && file_exists("$path/src/");
            }

            public function loadPlugin(string $file) : void{
                $description = $this->getPluginDescription($file);
                if($description !== null){
                    Server::getInstance()->getLoader()->addPath($description->getSrcNamespacePrefix(), "$file/src");
                }
            }

            public function getPluginDescription(string $file) : ?PluginDescription{
                if(is_dir($file) && file_exists("$file/plugin.yml")){
                    $yaml = file_get_contents("$file/plugin.yml");
                    if($yaml !== ""){
                        return new PluginDescription($yaml);
                    }
                }

                return null;
            }

            public function getAccessProtocol() : string{
                return "";
            }
        });
    }
}
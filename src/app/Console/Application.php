<?php

declare(strict_types=1);

namespace MagedIn\Lab\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    private string $logo = '
    __  ___                     ______         __  ___                 __          __  
   /  |/  /___ _____ ____  ____/ /  _/___     /  |/  /___ _____ ____  / /   ____ _/ /_ 
  / /|_/ / __ `/ __ `/ _ \/ __  // // __ \   / /|_/ / __ `/ __ `/ _ \/ /   / __ `/ __ \
 / /  / / /_/ / /_/ /  __/ /_/ // // / / /  / /  / / /_/ / /_/ /  __/ /___/ /_/ / /_/ /
/_/  /_/\__,_/\__, /\___/\__,_/___/_/ /_/  /_/  /_/\__,_/\__, /\___/_____/\__,_/_.___/ 
             /____/                                     /____/                         


';

    /**
     * @return string
     */
    public function getHelp()
    {
        return $this->logo . parent::getHelp();
    }

    /**
     * @return string
     */
    public function getLongVersion()
    {
        return parent::getLongVersion() . ' by <info>MagedIn Technology</info>';
    }
}

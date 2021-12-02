<?php
namespace App\Classes\Robots;

use Exception;
use Illuminate\Support\Facades\Storage;

class BaseRobot
{
    protected $locked = false;
    public function execute(){}
    private $options = [];
    public static $colors = [
        'default' => [
            'start' => '',
            'end'   => ''
        ],
        'danger' => [
            'start' => "\033[91m",
            'end'   => "\033[0m"
        ],
        'warning' => [
            'start' => "\033[93m",
            'end'   => "\033[0m"
        ],
        'info' => [
            'start' => "\033[96m",
            'end'   => "\033[0m"
        ],
        'success' => [
            'start' => "\033[92m",
            'end'   => "\033[0m"
        ],
    ];

    public static function getMemoryUsage()
    {
                $size = \memory_get_usage(true);
        $unit=array('b','kb','mb','gb','tb','pb');
        self::cli_message(@round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i],'info');
    }

    public function run()
    {

        self::cli_message('Iniciando execução do robo ['.$this->getChildName().']','success');

        if($this->getOption('force-run')){
            $this->locked = false;  
        }
        if($this->getOption('force-remove')){
            $this->removeLock();
        }
        if(!$this->addLock()){
            return false;
        }
        try{
            $this->execute();
            $this->removeLock();
        }catch(Exception $e){
            self::cli_message(sprintf("Erro ao executar script: %s \n Linha: %s \n arquivo: %s \n",$e->getMessage(),$e->getLine(),$e->getFile()),'danger');
            $this->removeLock();
        }
}


    private function addLock()
    {
        if($this->locked){
            $name = $this->getChildName();
            $lock = $name.".lock";
            $path = realpath(__DIR__.'/../../../storage/app/public/temp/');
            if(file_exists($path.'/'.$lock)){
                self::cli_message(sprintf("O script [%s.php] já está em execução.",$name),'warning');
                return false;
            }
            self::cli_message(sprintf("Criando arquivo de lock [%s]",$lock),'info');
            $lockFile = fopen($path."/".$lock,"w");
            fclose($lockFile);
        }
        return true;
    }
    
    private function removeLock()
    {
        $name = $this->getChildName();
        $lock = $name.".lock";
        $path = realpath(__DIR__.'/../../../storage/app/public/temp/');
        if(file_exists($path.'/'.$lock)){
            self::cli_message(sprintf("Removendo lock do robo [%s.php]",$name),'info');
            unlink($path.'/'.$lock);
        }

    }
    public static function cli_message($msg,$color = 'default')
    {
        printf("%s[%s] %s%s \n",self::$colors[$color]['start'],date('Y-m-d H:i:s'),$msg,self::$colors[$color]['end']);
    }
    private function getChildName()
    {
        $child = get_called_class();
        $child = explode("\\",$child);
        $len = count($child) - 1;
        return $child[$len];
    }

    public function getOption($value)
    {
        return $this->options[$value];
    }

    public function setOption($option,$value)
    {
        $this->options[$option] = $value;
    }

}

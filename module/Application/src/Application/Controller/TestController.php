<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Application\Models\Users;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;

use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\Adapter\Memcached;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\Storage\AvailableSpaceCapableInterface;
use Zend\Cache\Storage\FlushableInterface;
use Zend\Cache\Storage\TotalSpaceCapableInterface;
/*
$this->params()->fromPost('paramname');   // From POST
$this->params()->fromQuery('paramname');  // From GET
$this->params()->fromRoute('paramname');  // From RouteMatch
$this->params()->fromHeader('paramname'); // From header
$this->params()->fromFiles('paramname');
*/
class TestController extends AbstractActionController
{
################################################################################ 
    public function __construct()
    {
        $this->cacheTime = 1000;
        $this->now = date("Y-m-d H:i:s");
        $this->config = include __DIR__ . '../../../../config/module.config.php';
    }
################################################################################
    public function basic()
    {
        echo "=== TEST CONTROLLER ===";
        $view = new ViewModel();
        return $view;       
    } 
################################################################################
    public function indexAction() 
    {
        try
        {
            $view = $this->basic();
            $act = $this->params()->fromQuery('act', '');

            $cache = $this->maMemCache($this->cacheTime, 'answer');
            $data1 = $cache->getItem('answer1', $success);
            $data2 = $cache->getItem('answer2', $success);
            
            if ($data1)
            {
                echo "<br/>=> answer1 from cache";
                $view->answer1 = $data1;
            }
                
            if ($data2)
            {
                echo "<br/>=> answer2 from cache";
                $view->answer2 = $data2;
            }

            if ($act == 'question1')
            {
                echo "<br/>=> answer1 from button 'Calculate'";
                $result = $this->calQuestion1();
                $view->answer1 = $result;
                $cache->setItem('answer1', $result);
            }
            else if ($act == 'question2')
            {
                echo "<br/>=> answer2 from button 'Calculate'";
                $result = $this->calQuestion2();
                $view->answer2 = $result;
                $cache->setItem('answer2', $result);
            }

            return $view;
        }
        catch( Exception $e )
        {
            print_r($e);
        }
    }
################################################################################
    public function calQuestion1() // Find X from Array [3, 5, 9, 15, X]
    {
        $items = array(3, 5, 9, 15);
        $x = 0;
        $text = "[3";
        
        for ($i = 0; $i < count($items); $i++)
        {
            $x = $items[$i] + (2*($i+1));
            $text = $text.", ".$x;
        }
      
        $text = $text."]";
        echo " === ".$text;

        return $x;
    }
################################################################################
    public function calQuestion2() // Find X from (X + 24)+(10 × 2) = 99
    {
        $x = 99-(10*20)-24;
        return $x;
    }
################################################################################
    function maMemCache($time, $namespace)
    {
        $cache = StorageFactory::factory([
											    'adapter' => [
											        'name' => 'filesystem',
											        'options' => [
											            'namespace' => $namespace,
											            'ttl' => $time,
											        ],
											    ],
											    'plugins' => [
											        // Don't throw exceptions on cache errors
											        'exception_handler' => [
											            'throw_exceptions' => true
											        ],
											        'Serializer',
											    ],
											]);
		return($cache);
	}
################################################################################
}
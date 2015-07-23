<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreReactionModule {

	protected $app;
	protected $db;
	protected $config;

    protected $container;
    protected $tower;
    protected $inputs = array();
    protected $output;
    protected $contentType;
    protected $value = 0;
    protected $modifier;

    protected $reactop =array(
		"16670" => 100,
		"17317" => 2,
		"16673" => 100,
		"16683" => 4,
		"16679" => 30,
		"16682" => 7.5,
		"16681" => 15,
		"33362" => 3,
		"16680" => 22,
		"33359" => 3,
		"16678" => 60,		// Sylramic Fibers
		"17966" => 60,
		"33360" => 3,
		"16671" => 100,
		"16672" => 100,
		"33361" => 3,
		"32824" => 0.01,
		"32829" => 0.01,
		"29660" => 0.01,
		"32828" => 0.01,
		"29663" => 0.01,
		"29659" => 0.01,
		"32825" => 0.01,
		"29664" => 0.01,
		"29661" => 0.01,
		"32822" => 0.01,
		"33338" => 0.01,
		"29662" => 0.01,
		"32826" => 0.01,
		"32823" => 0.01,
		"33339" => 0.01,
		"32827" => 0.01,
		"32821" => 0.01
	);

    public function __construct (RenaApp $app, $container, $tower) {
		$this->app = $app;
		$this->db = $this->app->Db;
		$this->config = $this->app->baseConfig;

        $this->container = $container;
        $this->tower = $tower;

        if($this->getTypeId() == 416) { // moon harvester
            $this->value = 100;
        }

        $this->modifier = (100 + ($this->getTypeId() == 404 &&
					!is_null($this->app->CoreManager->getDGMAttribute($this->pos->getTypeId(), 757)) ?
						$this->app->CoreManager->getDGMAttribute($this->pos->getTypeId(), 757)['valueFloat'] :
						0
					)
				) / 100;
    }

    public function append ($containerID, $container) {
        if($this->container->getId() == $containerID) {
            $container->setOutput($this);
            array_push($this->input, $container);
            return true;
        } else {
            foreach ($this->inputs as $input) {
                $ret = $input->append($containerID, $container);
                if($ret)
                    return true;
            }
        }
    }

    public function setOutput ($container) {
        $this->output = $container;
    }

    public function process () {
        foreach ($this->inputs as $input)
            $input->process();

        if($this->getTypeId() == 438) { // reactor
            if(count($this->container->getContents()) > 0) {
                $inputMaterials = $this->db->query("SELECT * FROM invTypeReactions WHERE reactionTypeID = :typeID ORDER BY input DESC", array(":typeID" => $this->container->getContents()[0]->getTypeId()));
                if(count($inputMaterials) == count($this->input) + 1) {
                    $inputEmpty = false;
                    $usedInputs = array();
                    foreach ($inputMaterials as $ingredient) {
                        if($ingredient['input'] == 1) { // input
                            foreach ($this->inputs as $input) {
                                if(!in_array($usedInputs, $input)) {
                                    if($input->getTypeId() == 404) { // silo
                                        if($input->getValue() < $ingredient['quantity'] && $input->getQuantity() < $ingredient['quantity']) {
                                            $inputEmpty = true;
                                        } else if(
                                            ($input->getValue() >= $ingredient['quantity'] || $input->getQuantity() >= $ingredient['quantity']) &&
                                            $input->getContentsTypeId() == $ingredient['typeID']
                                        ) {
                                            $input->modify(-(
                                                isset($this->reactop[$ingredient['typeID']]) ? $this->reactop[$ingredient['typeID']] : $ingredient['quantity']
                                            ) * (in_array($this->app->CoreManager->getItemType($ingredient['reactionTypeID'])->getMarketGroupId(), array(/*1850, 1851, */1852, 1853, 1854)) ? 1 : 100)
                                            );
                                        }
                                    }
                                }
                            }
                        } else { // output
                            if($this->output && !$inputEmpty)
                                $this->output->output((
                                    isset($this->reactop[$ingredient['typeID']]) ? $this->reactop[$ingredient['typeID']] : $ingredient['quantity']
                                ) * (in_array($this->app->CoreManager->getItemType($ingredient['reactionTypeID'])->getMarketGroupId(), array(/*1850, 1851, */1852, 1853, 1854)) ? 1 : 100)
                                , (int)$ingredient['typeID']);
                        }
                    }
                }
            }
        } else if($this->getTypeId() == 416) { // moon harvester
            $this->output(100);
        } else if($this->getTypeId() == 404) { // silo
            // nothing to do here
        }
    }

    public function export (&$arr) {
        if(
            $this->getTypeId() == 404 && // silo
            (!$this->output || $this->output->getTypeId() != 404) &&
            count($this->container->getContents()) > 0 &&
            (
                (
                    count($this->container->getContents()) > 0 && !is_null($this->getContentsTypeId())
                )
            )
        ) {
            array_push($arr,
                array(
                    "id" => $this->container->getId(),
                    "location" => $this->tower->getId(),
                    "value" => $this->value,
                    "left" => floor($this->value == 0 ? 0 : -(
                        (($this->value > 0 ?
                            -($this->getCargo() / $this->container->getContents()[0]->getVolume()) :
                            0
                        ) + $this->getQuantity()) / $this->value
                    )),
                    "state" => ($this->isEmpty() && $this->value != 0 ? "empty" : ($this->isFull() ? "full" : ($this->value == 0 ? "inactive" : "running"))),
                    "ts" => $this->container->getTimestamp()
                )
            );
        }
    }

    public function getCargo () {
        return ($this->getTypeId() == 404 ? // silo
            ($this->container->getCapacity() * $this->modifier + (count($this->inputs) == 1 ?
                $this->inputs[0]->getCargo() : 0
            ) : 0
        );
    }

    public function isEmpty () {
        if(count($this->container->getContents()) == 0) return true;
        if(isset($this->contentType) && $this->contentType == 0) return true;
        return floor($this->getQuantity() / ($this->value == 0 ? 1 : $this->value)) == 0 ? true : false;
    }

    public function isFull () {
        return floor((($this->getCargo() - ($this->getQuantity() * $this->container->getContents()[0]->getVolume())) / $this->container->getContents()[0]->getVolume())) < $this->value * $this->container->getContents()[0]->getVolume() ? true : false;
    }

    public function output ($quantity, $typeID = null) {
        if($this->container->getTypeId() == 404) { // silo
            $this->modify($quantity);
            if(count($this->container->getContents()) > 0 && is_null($typeID))
                $this->contentType = $this->container->getContents()[0]->getTypeId();
            else
                $this->contentType = $typeID;
        }
        if(!is_null($this->output) && $this->output->getTypeId() == 404) { // silo
            $this->output->output($quantity, $typeID);
        }
    }

    public function modify ($value) {
        $this->value += $value;
    }

    public function getValue () {
        return $this->value;
    }

    public function getQuantity () {
        return
            ($this->getTypeId() == 404 ?
                (
                    count($this->container->getContents()) > 0 && !is_null($this->container->getContents()[0]->getQuantity()) ?
                        $this->container->getContents()[0]->getQuantity() :
                        0
                ) + (count($this->inputs) == 1 ? $this->inputs[0]->getQuantity() : 0)
            ) : 0
        );
    }

    public function getTypeId () {
        return $this->container->getTypeId();
    }

    public function getContentsTypeId () {
        return count($this->container->getContents()) > 0 ? $this->container->getContents()[0]->getTypeId() : null;
    }

}

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

    function __construct (RenaApp $app, $params = array()) {
		$this->app = $app;
		$this->db = $this->app->Db;
		$this->config = $this->app->baseConfig;

        $this->container = $params['container'];
        $this->tower = $params['tower'];

        if(!$this->container || !$this->tower) return;

        if($this->getTypeId() == 416) { // moon harvester
            $this->value = 100;
        }

        $this->modifier = (100 + ($this->getTypeId() == 404 &&
					count($this->app->CoreManager->getDGMAttribute($this->tower->getTypeId(), 757)) > 0 ?
						$this->app->CoreManager->getDGMAttribute($this->tower->getTypeId(), 757)['valueFloat'] :
						0
					)
				) / 100;
    }

    public function populateInputs () {
        $inputs = $this->db->query("SELECT * FROM easControltowerReactions WHERE destination = :destination AND towerID = :towerID", array(":destination" => $this->container->getId(), ":towerID" => $this->tower->getId()));
        foreach ($inputs as $input) {
            $inputMod = new CoreReactionModule($this->app, array("container" => $this->app->CoreManager->getContainer((int)$input['source']), "tower" => $this->tower));
            $inputMod->setOutput($this);
            $inputMod->populateInputs();
            array_push($this->inputs, $inputMod);
        }
    }

    public function setOutput ($container) {
        $this->output = $container;
    }

    public function processReaction () {
        foreach ($this->inputs as $input)
            $input->processReaction();

        switch ($this->getTypeId()) {
            case 438: // reactor

                // if reactor empty, stop here
                if(!$this->hasContents()) break;

                // get ingredients
                $ingredients = $this->db->query("SELECT * FROM invTypeReactions WHERE reactionTypeID = :typeID ORDER BY input DESC", array(":typeID" => $this->getContents()[0]->getTypeId()));

                // if (input + output connections) are not there, stop here
                if(count($ingredients) != count($this->inputs) + 1) break;

                // when input is empty, dont set output later
                $inputIsEmpty = false;

                // list of the inputs that are already used up
                $usedInputs = array();

                foreach ($ingredients as $ingredient) {
                    switch ((int)$ingredient['input']) {
                        case 1: // is input

                            // loop over inputs and assort them
                            foreach ($this->inputs as $input) {

                                // if input was already used, skip
                                if(in_array($input->getId(), $usedInputs)) continue;

                                // if input is not a silo, skip
                                if($input->getTypeId() != 404) continue;

                                if($input->getInputValue() < $ingredient['quantity'] && $input->getRecQuantity() < $ingredient['quantity']) {
                                    $inputIsEmpty = true;
                                } else if(
                                    ($input->getInputValue() >= $ingredient['quantity'] || $input->getRecQuantity() >= $ingredient['quantity']) &&
                                    $input->hasContents() && $input->getContents()[0]->getTypeId() == $ingredient['typeID']
                                ) {

                                    $ingredientQuantity = -(isset($this->reactop[$ingredient['typeID']]) ? $this->reactop[$ingredient['typeID']] : $ingredient['quantity']);
                                    $ingredientMultiplier = in_array($this->app->CoreManager->getItemType($ingredient['reactionTypeID'])->getMarketGroupId(), array(/*1850, 1851, */1852, 1853, 1854)) ? 1 : 100;

                                    $requiredMats = $ingredientQuantity * $ingredientMultiplier;

                                    $input->addInputValue($requiredMats);

                                }

                            }

                            break;
                        case 0: // is output

                            // if reactor has output and none of the inputs have been empty
                            if($this->output && !$inputIsEmpty) {

                                $ingredientQuantity = isset($this->reactop[$ingredient['typeID']]) ? $this->reactop[$ingredient['typeID']] : $ingredient['quantity'];
                                $ingredientMultiplier = in_array($this->app->CoreManager->getItemType($ingredient['reactionTypeID'])->getMarketGroupId(), array(/*1850, 1851, */1852, 1853, 1854)) ? 1 : 100;

                                $outputMats = $ingredientQuantity * $ingredientMultiplier;

                                $this->output->addValueToOutput($outputMats);

                            }

                            break;
                    }
                }

                break;
            case 416: // moon harvester
                $this->addValueToOutput(100);
                break;
            case 404: // silo
                // nothing to do here
                break;
        }
    }

    public function exportReaction (&$arr) {

        if($this->output)
            var_dump("tid ".$this->output->getTypeId());

        var_dump(is_null($this->output));
        var_dump(count($this->inputs));

        if(
            $this->getTypeId() == 404 && // only export silos
            (is_null($this->output) || $this->output->getTypeId() != 404) && // only export the topmost silo (no output or a reactor as output)
            ($this->hasContents() || $this->inputValue != 0) // should have content or at least will have content
        ) {
            var_dump("export");

            array_push($arr,
                array(
                    "id"        => $this->getId(),
                    "name"      => $this->tower->getName(),
                    "location"  => $this->tower->getId(),
                    "value"     => $this->inputValue,
                    "left"      => $this->inputValue == 0 ? 0 : -(($this->inputValue > 0 ? -($this->getRecCargo() / $this->getRecContents()->getVolume()) : 0) / $this->inputValue),
                    "state"     => is_null($this->getRecContents()) && $this->inputValue != 0 ? "empty" :
                        (
                            (floor((($this->getRecCargo() - ($this->getRecQuantity() * $this->getRecContents()->getVolume())) /  $this->getRecContents()->getVolume())) < $this->inputValue * $this->getRecContents()->getVolume() ? true : false) ?
                                "full" : ($this->inputValue == 0 ? "inactive" : "running")
                        ),
                    "ts"        => $this->container->getTimestamp()
                )
            );

        }

        var_dump("move on");

        foreach ($this->inputs as $input)
            $input->exportReaction($arr);
    }

    public function getTypeId () {
        return $this->container->getType()->getGroupId();
    }

    protected $inputValue = 0;

    public function addInputValue ($value) {
        $this->inputValue += $value;
    }

    public function getInputValue () {
        return $this->inputValue;
    }

    public function getContents () {
        return $this->container->getContents();
    }

    public function hasContents () {
        return count($this->getContents()) == 0 ? false : true;
    }

    public function getId () {
        return $this->container->getId();
    }

    public function addValueToOutput ($value) {
        $this->output->addInputValue($value);
    }

    public function getRecQuantity () {
        return $this->getTypeId() == 404 ? // only do if silo
                (
                    $this->hasContents() && !is_null($this->container->getContents()[0]->getQuantity()) ?
                        $this->container->getContents()[0]->getQuantity() :
                        0
                ) + (count($this->inputs) == 1 ? $this->inputs[0]->getRecQuantity() : 0) :
                0;
    }

    public function getRecCargo () {
        return $this->getTypeId() == 404 ? // silo
            ($this->container->getType()->getCapacity() * $this->modifier + (count($this->inputs) == 1 ?
                $this->inputs[0]->getRecCargo() : 0
            )) :
            0;
    }

    public function getRecContents () {
        if($this->getTypeId() != 404) return null;
        if($this->hasContents())
            return $this->getContents()[0]->getType();
        else if(count($this->inputs) == 1)
            return $this->inputs[0]->getRecContents();
    }

    public function RunAsNew () {

    }

}

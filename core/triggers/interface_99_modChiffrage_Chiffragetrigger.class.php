<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		core/triggers/interface_99_modMyodule_Chiffragetrigger.class.php
 * 	\ingroup	chiffrage
 * 	\brief		Sample trigger
 * 	\remarks	You can create other triggers by copying this one
 * 				- File name should be either:
 * 					interface_99_modMymodule_Mytrigger.class.php
 * 					interface_99_all_Mytrigger.class.php
 * 				- The file must stay in core/triggers
 * 				- The class name must be InterfaceMytrigger
 * 				- The constructor method must be named InterfaceMytrigger
 * 				- The name property name must be Mytrigger
 */

/**
 * Trigger class
 */
class InterfaceChiffragetrigger
{

    private $db;
	const  PROGRESS_CHANGE = 1;
    /**
     * Constructor
     *
     * 	@param		DoliDB		$db		Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;

        $this->name = preg_replace('/^Interface/i', '', get_class($this));
        $this->family = "demo";
        $this->description = "Triggers of this module are empty functions."
            . "They have no effect."
            . "They are provided for tutorial purpose only.";
        // 'development', 'experimental', 'dolibarr' or version
        $this->version = 'development';
        $this->picto = 'chiffrage@chiffrage';
    }

    /**
     * Trigger name
     *
     * 	@return		string	Name of trigger file
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Trigger description
     *
     * 	@return		string	Description of trigger file
     */
    public function getDesc()
    {
        return $this->description;
    }

    /**
     * Trigger version
     *
     * 	@return		string	Version of trigger file
     */
    public function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'development') {
            return $langs->trans("Development");
        } elseif ($this->version == 'experimental')

                return $langs->trans("Experimental");
        elseif ($this->version == 'dolibarr') return DOL_VERSION;
        elseif ($this->version) return $this->version;
        else {
            return $langs->trans("Unknown");
        }
    }

    /**
     * Function called when a Dolibarrr business event is done.
     * All functions "run_trigger" are triggered if file
     * is inside directory core/triggers
     *
     * 	@param		string		$action		Event action code
     * 	@param		Object		$object		Object
     * 	@param		User		$user		Object user
     * 	@param		Translate	$langs		Object langs
     * 	@param		conf		$conf		Object conf
     * 	@return		int						<0 if KO, 0 if no triggered ran, >0 if OK
     */
    public function run_trigger($action, $object, $user, $langs, $conf)
    {
    	global $mysoc;
        // Put here code you want to execute when a Dolibarr business events occurs.
        // Data and type of action are stored into $object and $action
		if(! defined('INC_FROM_DOLIBARR')) define('INC_FROM_DOLIBARR', true);
		dol_include_once('/chiffrage/class/chiffrage.class.php');
		switch ($action){
			case 'TASK_MODIFY':
				dol_syslog(
					"Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id
				);

				GETPOST($object->array_options['options_fk_chiffrage']);
				$oldChiffrage = $object->oldcopy->array_options['options_fk_chiffrage'];
				$fk_chiffrage = $object->array_options['options_fk_chiffrage'];
				if($oldChiffrage != $fk_chiffrage){
					$object->deleteObjectLinked($fk_chiffrage,'chiffrage', $object->id,'project_task');
					$object->add_object_linked('chiffrage', $fk_chiffrage);
				}

				$this->setStatusForObject($object,Chiffrage::STATUS_REALIZED,self::PROGRESS_CHANGE, $object->progress);
				break;

			case 'TASK_DELETE':
				$this->setStatusForObject($object,Chiffrage::STATUS_ESTIMATED);
				break;

			case 'PROPAL_DELETE':
				$this->setStatusForObject($object,Chiffrage::STATUS_ESTIMATED);
				break;

			case 'TASK_TIMESPENT_CREATE':
				$this->setStatusForObject($object,Chiffrage::STATUS_REALIZED,self::PROGRESS_CHANGE, $object->progress);
				break;

			case 'TASK_TIMESPENT_MODIFY':
				$this->setStatusForObject($object,Chiffrage::STATUS_REALIZED,self::PROGRESS_CHANGE, $object->progress);
				break;
		}

        return 0;
    }


	/**
	 * @param $object
	 * @param $status
	 * @param $progressChange
	 * @param $progress
	 * @return void
	 */
	private function setStatusForObject(&$object, $status, $progressChange = 0, $progress = 0){
		global $langs;

		if ( ($progressChange && $progress == 100) ||  (!$progressChange && !$progress)){
			$res  = $object->fetchObjectLinked($object->id);
			if ($res > 0 ){
				if (count($object->linkedObjectsIds['chiffrage_chiffrage']) > 0){
					$chiId = reset($object->linkedObjectsIds['chiffrage_chiffrage']);
					$Chi = new Chiffrage($this->db);
					$res = $Chi->fetch($chiId);
					if ($res >  0){
						$Chi->setStatut($status);
						setEventMessage($langs->transnoentities('CHITaskDeleteChangeStatusToEstimated', $Chi->getNomUrl(1, '', 0, 'ref'),$Chi->getLibStatut($status)));
					}
				}
			}
		}
	}


}

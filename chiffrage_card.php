<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *    \file       chiffrage_card.php
 *        \ingroup    chiffrage
 *        \brief      Page to create/edit/view chiffrage
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');				// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK', '1');				// Do not check CSRF attack (test on referer + on token if option MAIN_SECURITY_CSRF_WITH_TOKEN is on).
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL', '1');				// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK', '1');				// Do not check style html tag into posted data
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU', '1');				// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML', '1');				// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX', '1');       	  	// Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                  define("NOLOGIN", '1');					// If this page is public (can be called outside logged session). This include the NOIPCHECK too.
//if (! defined('NOIPCHECK'))                define('NOIPCHECK', '1');					// Do not check IP defined into conf $dolibarr_main_restrict_ip
//if (! defined("MAIN_LANG_DEFAULT"))        define('MAIN_LANG_DEFAULT', 'auto');					// Force lang to a particular value
//if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE', 'aloginmodule');	// Force authentication handler
//if (! defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN', 1);		// The main.inc.php does not make a redirect if not logged, instead show simple error message
//if (! defined("FORCECSP"))                 define('FORCECSP', 'none');				// Disable all Content Security Policies
//if (! defined('CSRFCHECK_WITH_TOKEN'))     define('CSRFCHECK_WITH_TOKEN', '1');		// Force use of CSRF protection with tokens even for GET
//if (! defined('NOBROWSERNOTIF'))     		 define('NOBROWSERNOTIF', '1');				// Disable browser notification

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
    $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
    $i--;
    $j--;
}
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
    $res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
    $res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) {
    $res = @include "../main.inc.php";
}
if (! $res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}
if (! $res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}
if (! $res) {
    die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formprojet.class.php';
require_once DOL_DOCUMENT_ROOT."/ticket/class/ticket.class.php";
require_once DOL_DOCUMENT_ROOT."/projet/class/task.class.php";
dol_include_once('/chiffrage/class/chiffrage.class.php');
dol_include_once('/chiffrage/lib/chiffrage_chiffrage.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("chiffrage@chiffrage", "other"));

// Get parameters
$fk_task = GETPOST('fk_task', 'int');
$fk_ticket = GETPOST('fk_ticket', 'int');
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'chiffragecard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
//$lineid   = GETPOST('lineid', 'int');

// Initialize technical objects
$object = new Chiffrage($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->chiffrage->dir_output . '/temp/massgeneration/' . $user->id;
$hookmanager->initHooks(array('chiffragecard', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

// Initialize array of search criterias
$search_all = GETPOST("search_all", 'alpha');
$search = array();
foreach ($object->fields as $key => $val) {
    if (GETPOST('search_' . $key, 'alpha')) {
        $search[$key] = GETPOST('search_' . $key, 'alpha');
    }
}

if (empty($action) && empty($id) && empty($ref)) {
    $action = 'view';
}

// Load object
include DOL_DOCUMENT_ROOT . '/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

$permissiontoread = $user->rights->chiffrage->chiffrage->read;
$permissiontoadd = $user->rights->chiffrage->chiffrage->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
$permissiontodelete = $user->rights->chiffrage->chiffrage->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
$permissionnote = $user->rights->chiffrage->chiffrage->write; // Used by the include of actions_setnotes.inc.php
$permissiondellink = $user->rights->chiffrage->chiffrage->write; // Used by the include of actions_dellink.inc.php
$upload_dir = $conf->chiffrage->multidir_output[isset($object->entity) ? $object->entity : 1] . '/chiffrage';

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->element, $object->id, $object->table_element, '', 'fk_soc', 'rowid', $isdraft);
//if (empty($conf->chiffrage->enabled)) accessforbidden();
//if (!$permissiontoread) accessforbidden();

/*
 * Actions
 */
$addNew = GETPOSTISSET('addnew');
$addCancel = GETPOSTISSET('cancel');

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
    setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
    $error = 0;

    $backurlforlist = dol_buildpath('/chiffrage/chiffrage_list.php', 1);

    if (empty($backtopage) || ($cancel && empty($id))) {
        if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
            if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
                $backtopage = $backurlforlist;
            } else {
                $backtopage = dol_buildpath('/chiffrage/chiffrage_card.php', 1) . '?id=' . ($id > 0 ? $id : '__ID__');
            }
        }
    }

    $triggermodname = 'CHIFFRAGE_CHIFFRAGE_MODIFY'; // Name of trigger action code to execute when we modify record

    // Action clone object and redirect to edit mode
    if ($action == 'confirm_clone' && $confirm == 'yes' && ! empty($permissiontoadd)) {
        if (1 == 0 && ! GETPOST('clone_content') && ! GETPOST('clone_receivers')) {
            setEventMessages($langs->trans("NoCloneOptionsSpecified"), null, 'errors');
        } else {
            $objectutil = dol_clone($object, 1); // To avoid to denaturate loaded object when setting some properties for clone or if createFromClone modifies the object. We use native clone to keep this->db valid.
            $result = $objectutil->createFromClone($user, (($object->id > 0) ? $object->id : $id));
            if (is_object($result) || $result > 0) {
                $newid = 0;
                if (is_object($result)) $newid = $result->id;
                else $newid = $result;
                header("Location: " . $_SERVER['PHP_SELF'] . '?id=' . $newid . '&action=edit'); // Open record of new object
                exit;
            } else {
                setEventMessages($objectutil->error, $objectutil->errors, 'errors');
                $action = '';
            }
        }
    }

	// Action Création d'une propale depuis un chiffrage
	if ($action == 'create_propal_from_chiffrage') {
		if (!empty($user->rights->propal->creer) && !empty($object->fk_soc)) {
			$propalFromChiffrage = new Propal($db);
			$propalFromChiffrage->socid = $object->fk_soc;
			$propalFromChiffrage->date = dol_now();
			$propalFromChiffrage->datep = $propalFromChiffrage->date;
			$propalFromChiffrage->date_validation = 90;
			$resCreate = $propalFromChiffrage->create($user);
			if($resCreate > 0){

				$product = new Product($db);
				$resprod = $product->fetch($object->fk_product);

				// Ajout de la ligne à la propale avec extrafield : fk_chiffrage pour la liaison avec le chiffrage
				$resAddline = $propalFromChiffrage->addline(
					$object->commercial_text,
					$product->price,
					$object->qty,
					$product->tva_tx,
					0,
					0,
					$object->fk_product,
					0.0,
					'HT',
					0.0,
					0,
					$product->type,
					-1,
					0,
					0,
					0,
					$product->cost_price,
					'',
					'',
					'',
					array('options_fk_chiffrage' => $object->id)
				);
				$object->add_object_linked('propal',$propalFromChiffrage->id);
				$backtopage = dol_buildpath('/comm/propal/card.php', 1) . '?id=' . $propalFromChiffrage->id;
				header("Location: " . $backtopage);
				exit;
			}
			else{
				setEventMessage($langs->trans('ErrorOnCreatePropal') . ' : '. $propalFromChiffrage->errorsToString(), 'errors');
			}
		}
		else{
			setEventMessage('NotEnoughRights', 'errors');
		}
	}

	// Action Création d'une tâche depuis un chiffrage
	if ($action == 'confirm_create_task') {
		$taskFromChiffrage = new Task($db);
		$taskFromChiffrage->fk_project = GETPOST('fk_projet', 'int');
		$labelTaskFromChiffrage = new Product($db);
		$resLabel = $labelTaskFromChiffrage->fetch($object->fk_product);

		if($resLabel > 0){
			$projectFromChiffrage = new Project($db);
			$resProjectFromChiffrage = $projectFromChiffrage->fetch($taskFromChiffrage->fk_project);
			if ($resLabel > 0){
				if ($projectFromChiffrage->statut == Project::STATUS_CLOSED) {
					setEventMessage($langs->trans("CHIErrorProjectClosed"), 'errors');
				} else {
					//Permet de générer le prochain numéro de référence
					$obj = empty($conf->global->PROJECT_TASK_ADDON) ? 'mod_task_simple' : $conf->global->PROJECT_TASK_ADDON;
					if (!empty($conf->global->PROJECT_TASK_ADDON) && is_readable(DOL_DOCUMENT_ROOT . "/core/modules/project/task/" . $conf->global->PROJECT_TASK_ADDON . ".php")) {
						require_once DOL_DOCUMENT_ROOT . "/core/modules/project/task/" . $conf->global->PROJECT_TASK_ADDON . '.php';
						$modTask = new $obj;
						$defaultref = $modTask->getNextValue(0, $taskFromChiffrage);
					}

					$taskFromChiffrage->ref = $defaultref;
					$taskFromChiffrage->label = $labelTaskFromChiffrage->label;
					$taskFromChiffrage->fk_task_parent = 0;

					if(!empty($object->commercial_text)) {
						if(!empty($taskFromChiffrage->description)){
							$taskFromChiffrage->description.= "\n";
						}
						$taskFromChiffrage->description .= '<h4>' . $langs->trans('CHICommercialText') . '</h4>'."\n";
						$taskFromChiffrage->description .= $object->commercial_text;
					}

					if(!empty($object->detailed_feature_specification)){
						if(!empty($taskFromChiffrage->description)){
							$taskFromChiffrage->description.= "\n";
						}
						$taskFromChiffrage->description.= '<h4>'.$langs->trans('DetailedFeatureSpecification').'</h4>'."\n";
						$taskFromChiffrage->description.= $object->commercial_text;
					}

					if(!empty($object->tech_detail)){
						if(!empty($taskFromChiffrage->description)){
							$taskFromChiffrage->description.= "\n";
						}
						$taskFromChiffrage->description.= '<h4>'.$langs->trans('CHITechDetail').'</h4>'."\n";
						$taskFromChiffrage->description.= $object->tech_detail;
					}


					//Ajout de l'extrafield chiffrage sur tâche en cours de création
					$taskFromChiffrage->array_options['options_fk_chiffrage'] = $object->id;

					$taskFromChiffrage->planned_workload = ($conf->global->CHIFFRAGE_DEFAULT_MULTIPLICATOR_FOR_TASK * 3600) * $object->qty;
					if($taskFromChiffrage->fk_project != -1){
						$res = $taskFromChiffrage->create($user);
						if ($res > 0) {
							$object->add_object_linked('project_task', $taskFromChiffrage->id);
							$backtopage = dol_buildpath('/projet/tasks/task.php', 1) . '?id=' . $taskFromChiffrage->id;
							header("Location: " . $backtopage);
							exit;
						} else {
							setEventMessage($langs->trans("CHIErrorCreateTask"), 'errors');
						}
					}else{
						setEventMessage($langs->trans("CHIErrorNoProject"), 'errors');
					}
				}
			}else{
				setEventMessage($langs->trans("CHIErrorFetchProject"), 'errors');
			}
		}else{
			setEventMessage($langs->trans("CHIErrorFetchLabelProduct"), 'errors');
		}
	}


    if ($action == 'create') {
		$object->fields['po_estimate']['default'] = $user->id;
		$object->fields['fk_product']['default'] = $conf->global->CHIFFRAGE_DEFAULT_PRODUCT;
		$object->fields['tech_detail']['visible'] = 5;
    }
	if($action == 'add' && $addCancel){
		header("Location: " . $backtopage); // Open record of new object
		exit;
	}

    if ($action == 'add') {
        $object->fields['tech_detail']['visible'] = 5;
		if($fk_ticket > 0){
			$object->fk_ticket = $fk_ticket;
			$backtopage = 'chiffrage_card.php?id=__ID__';
		}
    }
	if($action == 'set_ticket'){
		$object->add_object_linked('ticket',$fk_ticket);
		$url = $backtopage;
		if(empty($backtopage)){
			$url = dol_buildpath('/chiffrage/chiffrage_card.php', 1) . '?id='. $object->id;
		}
		header("Location: " . $url); // Open record of new object
		exit;
	}
    if ($action == 'add' && $addNew) {
        $backtopage = dol_buildpath('/chiffrage/chiffrage_card.php', 1) . '?action=create';
        $backtopage .= '&po_estimated=' . GETPOST('po_estimated');
        $backtopage .= '&fk_soc=' . GETPOST('fk_soc');
        $backtopage .= '&fk_project=' . GETPOST('fk_project');
        $backtopage .= '&fk_product=' . GETPOST('fk_product');
    }

    $saveAddNew = GETPOSTISSET('saveaddnew');
    $redirectBackToPage = null;
    if ($action == 'update' && $saveAddNew) {
        $redirectBackToPage = true;
    }

    if ($action === 'update') {

        $qty = GETPOST('qty', 'int');
        $dev = GETPOST('dev_estimate', 'int');
        $textDev = GETPOST('tech_detail');
        $textCommercial = GETPOST('commercial_text');

        if ($qty > 0 && $dev == -1) {
            setEventMessage($langs->trans("CHIErrorDevEstimate"), 'errors');
            $action = 'edit';
        }

        if ($qty > 0 && empty($textDev)) {
            setEventMessage($langs->trans("CHIErrorTechDetail"), 'errors');
            $action = 'edit';
        }
        if ($qty < 0) {
            setEventMessage($langs->trans("CHIErrorQty"), 'errors');
            $action = 'edit';
        }
    }
    // Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
    include DOL_DOCUMENT_ROOT . '/core/actions_addupdatedelete.inc.php';


	if($action == 'add' && $fk_ticket > 0){
		$object->add_object_linked('ticket',$fk_ticket);
	}

	if($action == 'confirm_create_task'){
		$object->add_object_linked('project_task',$fk_task);
	}

    // Actions when linking object each other
    include DOL_DOCUMENT_ROOT . '/core/actions_dellink.inc.php';

    // Actions when printing a doc from card
    include DOL_DOCUMENT_ROOT . '/core/actions_printing.inc.php';

    // Action to move up and down lines of object
    //include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';

    // Action to build doc
    include DOL_DOCUMENT_ROOT . '/core/actions_builddoc.inc.php';

    if ($action == 'set_thirdparty' && $permissiontoadd) {
        $object->setValueFrom('fk_soc', GETPOST('fk_soc', 'int'), '', '', 'date', '', $user, $triggermodname);
    }
    if ($action == 'classin' && $permissiontoadd) {
        $object->setProject(GETPOST('projectid', 'int'));
    }

    // Actions to send emails
    $triggersendname = 'CHIFFRAGE_CHIFFRAGE_SENTBYMAIL';
    $autocopy = 'MAIN_MAIL_AUTOCOPY_CHIFFRAGE_TO';
    $trackid = 'chiffrage' . $object->id;
    include DOL_DOCUMENT_ROOT . '/core/actions_sendmails.inc.php';
}

$saveAddNew = GETPOSTISSET('saveaddnew');
if ($redirectBackToPage == true && $textCommercial == ! null) {
    $backtopage = dol_buildpath('/chiffrage/chiffrage_card.php', 1) . '?action=create';
    $backtopage .= '&po_estimated=' . GETPOST('po_estimated');
    $backtopage .= '&fk_soc=' . GETPOST('fk_soc');
    $backtopage .= '&fk_project=' . GETPOST('fk_project');
    $backtopage .= '&fk_product=' . GETPOST('fk_product');
    header("Location: " . $backtopage); // Open record of new object
    exit;
}
/*
 * View
 *
 * Put here all code to build page
 */
$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);


$title = $langs->trans("Chiffrage");
$help_url = '';
llxHeader('', $title, $help_url,'','','','',array("chiffrage/css/chiffrage.css"));

// fields fk_soc & fk_project in view
$object->fields['fk_soc']['visible'] = 0;
$object->fields['fk_project']['visible'] = 0;

// Example : Adding jquery code
// print '<script type="text/javascript" language="javascript">
// jQuery(document).ready(function() {
// 	function init_myfunc()
// 	{
// 		jQuery("#myid").removeAttr(\'disabled\');
// 		jQuery("#myid").attr(\'disabled\',\'disabled\');
// 	}
// 	init_myfunc();
// 	jQuery("#mybutton").click(function() {
// 		init_myfunc();
// 	});
// });
// </script>';

// Part to create
if ($action == 'create') {
    $object->fields['fk_soc']['visible'] = 1;
    $object->fields['fk_project']['visible'] = 1;
    print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Chiffrage")), '', 'object_' . $object->picto);

    print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
    print '<input type="hidden" name="token" value="' . newToken() . '">';
    print '<input type="hidden" name="action" value="add">';

	if($fk_ticket > 0){
		print '<input type="hidden" name="fk_ticket" value="'.$fk_ticket.'">';
	}
    if ($backtopage) {
        print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
    }
    if ($backtopageforcancel) {
        print '<input type="hidden" name="backtopageforcancel" value="' . $backtopageforcancel . '">';
    }
    print dol_get_fiche_head(array(), '');

    // Set some default values
    //if (! GETPOSTISSET('fieldname')) $_POST['fieldname'] = 'myvalue';

    print '<table class="border centpercent tableforfieldcreate">' . "\n";

    // Ref
//    print '<tr><td class="titlefieldcreate fieldrequired">'.$langs->trans('Ref').'</td><td>'.$langs->trans("Draft").'</td></tr>';
//    $object->fields['ref']['visible'] = 0;
    $object->ref = $langs->trans("Draft");

    // Common attributes
    include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_add.tpl.php';

    // Other attributes
    include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_add.tpl.php';

    print '</table>' . "\n";

    print dol_get_fiche_end();

    print '<div class="center">';
    print '<input type="submit" class="button" name="add" value="' . dol_escape_htmltag($langs->trans("Create")) . '">';
    print '<input type="submit" class="button" name="addnew" value="' . dol_escape_htmltag($langs->trans("CHICreate&New")) . '">';
    print '&nbsp; ';
    print '<input type="' . ($backtopage ? "submit" : "button") . '" class="button button-cancel" name="cancel" value="' . dol_escape_htmltag($langs->trans("Cancel")) . '"' . ($backtopage ? '' : ' onclick="javascript:history.go(-1)"') . '>'; // Cancel for create does not post form if we don't know the backtopage
    print '</div>';

    print '</form>';
    //dol_set_focus('input[name="ref"]');
}

// Part to edit record
if (($id || $ref) && $action == 'edit') {
    $object->fields['fk_soc']['visible'] = 1;
    $object->fields['fk_project']['visible'] = 1;
    print load_fiche_titre($langs->trans("Chiffrage"), '', 'object_' . $object->picto);

    print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
    print '<input type="hidden" name="token" value="' . newToken() . '">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="id" value="' . $object->id . '">';
    if ($backtopage) {
        print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
    }
    if ($backtopageforcancel) {
        print '<input type="hidden" name="backtopageforcancel" value="' . $backtopageforcancel . '">';
    }

    // Make qty field visible if status is validated or estimated
    if ($object->status == $object::STATUS_VALIDATED || $object->status == $object::STATUS_ESTIMATED) {
        $object->fields['qty']['visible'] = 1;
        $object->fields['dev_estimate']['visible'] = 1;
        $object->fields['tech_detail']['visible'] = 1;
    } else {
        $object->fields['qty']['visible'] = 5;
        $object->fields['dev_estimate']['visible'] = 5;
        $object->fields['tech_detail']['visible'] = 5;
    }

    print dol_get_fiche_head();

    print '<table class="border centpercent tableforfieldedit">' . "\n";

    // Common attributes
    include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_edit.tpl.php';

    // Other attributes
    include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_edit.tpl.php';

    print '</table>';

    print dol_get_fiche_end();

    print '<div class="center"><input type="submit" class="button button-save" name="save" value="' . $langs->trans("Save") . '">';
    print '<input type="submit" class="button" name="saveaddnew" value="' . dol_escape_htmltag($langs->trans("CHISaveAddNew")) . '">';
    print ' &nbsp; <input type="submit" class="button button-cancel" name="cancel" value="' . $langs->trans("Cancel") . '">';
    print '</div>';

    print '</form>';
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create'))) {
    $res = $object->fetch_optionals();
    $object->fields['commercial_text']['position'] = 999;
    $object->fields['detailed_feature_specification']['position'] = 1000;
    $object->fields['tech_detail']['position'] = 1001;

    $head = chiffragePrepareHead($object);
    print dol_get_fiche_head($head, 'card', $langs->trans("Workstation"), -1, $object->picto);

    $formconfirm = '';

	// Action Création d'une tâche depuis un chiffrage
	if ($action == 'create_task_from_chiffrage') {
		include DOL_DOCUMENT_ROOT . '/core/actions_addupdatedelete.inc.php';
		$form = new Form($db);
		$refProjectChiffrage = new Project($db);
		$resProject = $refProjectChiffrage->fetch($object->fk_project);
		$formquestion = array(
			array(
				'type' => 'other',
				'name' => 'fk_projet',
			'label' => $langs->trans("CHISelectProject"),
			'value' => $form->selectForForms('Project:projet/class/project.class.php:1:t.fk_statut!=' . Project::STATUS_CLOSED, 'fk_projet',$object->fk_project, 1, '', '', "form-project")
		)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('CHITaskCreate'), $langs->trans('ConfirmCreateObject'), 'confirm_create_task', $formquestion, 0, 1);
	}

    // Confirmation to delete
    if ($action == 'delete') {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('CHIDeleteChiffrage'), $langs->trans('ConfirmDeleteObject'), 'confirm_delete', '', 0, 1);
    }
    // Confirmation to delete line
    if ($action == 'deleteline') {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id . '&lineid=' . $lineid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_deleteline', '', 0, 1);
    }
    // Clone confirmation
    if ($action == 'clone') {
        // Create an array for form
        $formquestion = array();
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneAsk', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
    }

    // Confirmation of action xxxx
    if ($action == 'xxx') {
        $formquestion = array();
        /*
        $forcecombo=0;
        if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
        $formquestion = array(
            // 'text' => $langs->trans("ConfirmClone"),
            // array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' => 1),
            // array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value' => 1),
            // array('type' => 'other',    'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockDecrease"), 'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1, 0, 0, '', 0, $forcecombo))
        );
        */
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('XXX'), $text, 'confirm_xxx', $formquestion, 0, 1, 220);
    }

    // Call Hook formConfirm
    $parameters = array('formConfirm' => $formconfirm, 'lineid' => $lineid);
    $reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
    if (empty($reshook)) {
        $formconfirm .= $hookmanager->resPrint;
    } elseif ($reshook > 0) {
        $formconfirm = $hookmanager->resPrint;
    }

    // Print form confirm
    print $formconfirm;

    // Object card
    // ------------------------------------------------------------
    $linkback = '<a href="' . dol_buildpath('/chiffrage/chiffrage_list.php', 1) . '?restore_lastsearch_values=1' . (! empty($socid) ? '&socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';

    $morehtmlref = '<div class="refidno">';

    // Ref customer
    //$morehtmlref .= $form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', 0, 1);
    //$morehtmlref .= $form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', null, null, '', 1);
    // Thirdparty
    $morehtmlref .= '<br>' . $langs->trans('ThirdParty') . ' : ' . (is_object($object->thirdparty) ? $object->thirdparty->getNomUrl(1) : '');
    // Project
    if (! empty($conf->projet->enabled)) {
        $langs->load("projects");
        $morehtmlref .= '<br>' . $langs->trans('Project') . ' ';
        if ($permissiontoadd) {
            //if ($action != 'classify') $morehtmlref.='<a class="editfielda" href="' . $_SERVER['PHP_SELF'] . '?action=classify&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> ';
            $morehtmlref .= ' : ';
            if ($action == 'classify') {
                //$morehtmlref .= $form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
                $morehtmlref .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '">';
                $morehtmlref .= '<input type="hidden" name="action" value="classin">';
                $morehtmlref .= '<input type="hidden" name="token" value="' . newToken() . '">';
                $morehtmlref .= $formproject->select_projects($object->socid, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
                $morehtmlref .= '<input type="submit" class="button valignmiddle" value="' . $langs->trans("Modify") . '">';
                $morehtmlref .= '</form>';
            } else {
                $morehtmlref .= $form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1);
            }
        } else {
            if (! empty($object->fk_project)) {
                $proj = new Project($db);
                $proj->fetch($object->fk_project);
                $morehtmlref .= ': ' . $proj->getNomUrl();
            } else {
                $morehtmlref .= '';
            }
        }
    }
    $morehtmlref .= '</div>';

    dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

    print '<div class="fichecenter">';
    print '<div class="fichehalfleft">';
    print '<div class="underbanner clearboth"></div>';
    print '<table class="border centpercent tableforfield">' . "\n";

    // Common attributes
    $keyforbreak = 'commercial_text';    // We change column just before this field
    //unset($object->fields['fk_project']);				// Hide field already shown in banner
    //unset($object->fields['fk_soc']);					// Hide field already shown in banner
    include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_view.tpl.php';

    // Other attributes. Fields from hook formObjectOptions and Extrafields.
    include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

    print '</table>';
    print '</div>';
    print '</div>';

    print '<div class="clearboth"></div>';

    print dol_get_fiche_end();

    /*
     * Lines
     */

    if (! empty($object->table_element_line)) {
        // Show object lines
        $result = $object->getLinesArray();

        print '	<form name="addproduct" id="addproduct" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . (($action != 'editline') ? '' : '#line_' . GETPOST('lineid', 'int')) . '" method="POST">
		<input type="hidden" name="token" value="' . newToken() . '">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline') . '">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="page_y" value="">
		<input type="hidden" name="id" value="' . $object->id . '">
		';

        if (! empty($conf->use_javascript_ajax) && $object->status == 0) {
            include DOL_DOCUMENT_ROOT . '/core/tpl/ajaxrow.tpl.php';
        }

        print '<div class="div-table-responsive-no-min">';
        if (! empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline')) {
            print '<table id="tablelines" class="noborder noshadow" width="100%">';
        }

        if (! empty($object->lines)) {
            $object->printObjectLines($action, $mysoc, null, GETPOST('lineid', 'int'), 1);
        }

        // Form to add new line
        if ($object->status == 0 && $permissiontoadd && $action != 'selectlines') {
            if ($action != 'editline') {
                // Add products/services form

                $parameters = array();
                $reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
                if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
                if (empty($reshook))
                    $object->formAddObjectLine(1, $mysoc, $soc);
            }
        }

        if (! empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline')) {
            print '</table>';
        }
        print '</div>';

        print "</form>\n";
    }

    // Buttons for actions

    if ($action != 'presend' && $action != 'editline') {
        print '<div class="tabsAction">' . "\n";
        $parameters = array();
        $reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
        if ($reshook < 0) {
            setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
        }

        if (empty($reshook)) {
            // Send
            if (empty($user->socid)) {
                print dolGetButtonAction($langs->trans('SendMail'), '', 'default', $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=presend&mode=init&token=' . newToken() . '#formmailbeforetitle');
            }

            // Back to draft
            if ($object->status == $object::STATUS_VALIDATED) {
                print dolGetButtonAction($langs->trans('SetToDraft'), '', 'default', $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=confirm_setdraft&confirm=yes&token=' . newToken(), '', $permissiontoadd);
            }

            print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=edit&token=' . newToken(), '', $permissiontoadd);

            // Validate
            if ($object->status == $object::STATUS_DRAFT) {
                if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
                    print dolGetButtonAction($langs->trans('Validate'), '', 'default', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=confirm_validate&confirm=yes&token=' . newToken(), '', $permissiontoadd);
                } else {
                    $langs->load("errors");
                    print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
                }
            }
			// Bouton Créer Devis (action = create_propal_from_chiffrage)
			if ($object->status == $object::STATUS_ESTIMATED && !empty($object->fk_soc)) {
				print dolGetButtonAction($langs->trans('CHICreatePropal'), '', 'default', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&socid=' . $object->socid . '&action=create_propal_from_chiffrage&token=' . newToken(), '', !empty($user->rights->propal->creer));
			}

			// Bouton Créer Tâche (action = create_task_from_chiffrage)
			if ($object->status == $object::STATUS_ESTIMATED) {
				print dolGetButtonAction($langs->trans('CHICreateTask'), '', 'default', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&fk_project=' . $object->fk_project . '&action=create_task_from_chiffrage&token=' . newToken(), '', $permissiontoadd);
			}

            // Clone
            print dolGetButtonAction($langs->trans('ToClone'), '', 'default', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&socid=' . $object->socid . '&action=clone&token=' . newToken(), '', $permissiontoadd);

            /*
            if ($permissiontoadd) {
                if ($object->status == $object::STATUS_ENABLED) {
                    print dolGetButtonAction($langs->trans('Disable'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=disable&token='.newToken(), '', $permissiontoadd);
                } else {
                    print dolGetButtonAction($langs->trans('Enable'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=enable&token='.newToken(), '', $permissiontoadd);
                }
            }
            if ($permissiontoadd) {
                if ($object->status == $object::STATUS_VALIDATED) {
                    print dolGetButtonAction($langs->trans('Cancel'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=close&token='.newToken(), '', $permissiontoadd);
                } else {
                    print dolGetButtonAction($langs->trans('Re-Open'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=reopen&token='.newToken(), '', $permissiontoadd);
                }
            }
            */

            // Delete (need delete permission, or if draft, just need create/modify permission)
            print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=delete&token=' . newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
        }
        print '</div>' . "\n";
    }

    // Select mail models is same action as presend
    if (GETPOST('modelselected')) {
        $action = 'presend';
    }

    if ($action != 'presend') {
        print '<div class="fichecenter"><div class="fichehalfleft">';
        print '<a name="builddoc"></a>'; // ancre

        $includedocgeneration = 0;

        // Documents
        if ($includedocgeneration) {
            $objref = dol_sanitizeFileName($object->ref);
            $relativepath = $objref . '/' . $objref . '.pdf';
            $filedir = $conf->chiffrage->dir_output . '/' . $object->element . '/' . $objref;
            $urlsource = $_SERVER["PHP_SELF"] . "?id=" . $object->id;
            $genallowed = $user->rights->chiffrage->chiffrage->read; // If you can read, you can build the PDF to read content
            $delallowed = $user->rights->chiffrage->chiffrage->write; // If you can create/edit, you can remove a file on card
            print $formfile->showdocuments('chiffrage:Chiffrage', $object->element . '/' . $objref, $filedir, $urlsource, $genallowed, $delallowed, $object->model_pdf, 1, 0, 0, 28, 0, '', '', '', $langs->defaultlang);
        }

        // Show links to link elements
        $somethingshown = $form->showLinkedObjectBlock($object);

        print '</div><div class="fichehalfright"><div class="ficheaddleft">';

        $MAXEVENT = 10;

        $morehtmlright = '<a href="' . dol_buildpath('/chiffrage/chiffrage_agenda.php', 1) . '?id=' . $object->id . '">';
        $morehtmlright .= $langs->trans("SeeAll");
        $morehtmlright .= '</a>';

        // List of actions on element
        include_once DOL_DOCUMENT_ROOT . '/core/class/html.formactions.class.php';
        $formactions = new FormActions($db);
        $somethingshown = $formactions->showactions($object, $object->element . '@' . $object->module, (is_object($object->thirdparty) ? $object->thirdparty->id : 0), 1, '', $MAXEVENT, '', $morehtmlright);

        print '</div></div></div>';
    }

    //Select mail models is same action as presend
    if (GETPOST('modelselected')) {
        $action = 'presend';
    }

    // Presend form
    $modelmail = 'chiffrage';
    $defaulttopic = 'InformationMessage';
    $diroutput = $conf->chiffrage->dir_output;
    $trackid = 'chiffrage' . $object->id;

    include DOL_DOCUMENT_ROOT . '/core/tpl/card_presend.tpl.php';
}

// End of page
llxFooter();
$db->close();

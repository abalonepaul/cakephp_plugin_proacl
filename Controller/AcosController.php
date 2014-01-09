<?php
/**
 *
 * @author Nicolas Rod <nico@alaxos.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://www.alaxos.ch
 * @property AclManagerComponent $AclManager
 */
/**
 *
 * @author Nicolas Rod <nico@alaxos.com>
 * @author Paul Marshall
 *
 */
class AcosController extends AclAppController {

    public $name = 'Acos';

    /**
     * Aco index method
     */
    public function admin_index() {

    }

    /**
     * Delete the Acos
     * @param string $run
     */
    function admin_empty_acos($run = null) {
        /* Delete ACO with 'alias' controllers -> all ACOs belonging to the
         * actions tree will be deleted, but eventual ACO that are not actions
         * will be kept */
        $controllerAco = $this->Aco->findByAlias('controllers');

        if ($controllerAco !== false) {
            $this->set('actionsExist', true);

            if (isset($run)) {
                if ($this->Aco->delete($controllerAco['Aco']['id'])) {
                    $this->set('actionsExist', false);

                    $this->Session->setFlash(
                        __d('acl',
                            'The actions in the ACO table have been deleted'),
                        'flash_message', null, 'plugin_acl');
                } else {
                    $this->Session->setFlash(
                        __d('acl',
                            'The actions in the ACO table could not be deleted'),
                        'flash_error', null, 'plugin_acl');
                }

                $this->set('run', true);
            } else {
                $this->set('run', false);
            }
        } else {
            $this->set('actionsExist', false);
        }
    }

    /**
     * Admin Build Acls
     * @param string $run
     */
    function admin_build_acl($run = null) {

        if (isset($run)) {
            $logs = $this->AclManager->createAcos();

            $this->set('logs', $logs);
            $this->set('run', true);
        } else {
            $missingAcoNodes = $this->AclManager->getMissingAcos();

            $this->set('missingAcoNodes', $missingAcoNodes);

            $this->set('run', false);
        }
    }

    /**
     * Prune the Acos
     * @param string $run
     */
    function admin_prune_acos($run = null) {

        if (isset($run)) {
            $logs = $this->AclManager->pruneAcos();

            $this->set('logs', $logs);
            $this->set('run', true);
        } else {
            $nodesToPrune = $this->AclManager->getAcosToPrune();

            $this->set('nodesToPrune', $nodesToPrune);

            $this->set('run', false);
        }
    }

    /**
     * Synchronize the Acos
     *
     * @param string $run
     */
    function admin_synchronize($run = null) {

        if (isset($run)) {
            $pruneLogs = $this->AclManager->pruneAcos();
            $createLogs = $this->AclManager->createAcos();

            $this->set('createLogs', $createLogs);
            $this->set('pruneLogs', $pruneLogs);

            $this->set('run', true);
        } else {
            $nodesToPrune = $this->AclManager->getAcosToPrune();
            $missingAcoNodes = $this->AclManager->getMissingAcos();

            $this->set('nodesToPrune', $nodesToPrune);
            $this->set('missingAcoNodes', $missingAcoNodes);

            $this->set('run', false);
        }
    }
}

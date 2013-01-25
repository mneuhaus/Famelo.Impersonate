<?php
namespace Famelo\Impersonate\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Famelo.Impersonate".    *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Standard controller for the Famelo.Impersonate package 
 *
 * @Flow\Scope("singleton")
 */
class StandardController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * @var \TYPO3\Flow\Security\AccountRepository
	 * @Flow\Inject
	 */
	protected $accountRepository;

	/**
	 * @var \Famelo\Impersonate\ImpersonateService
	 * @Flow\Inject
	 */
	protected $impersonateService;

	/**
	 * @var \TYPO3\Flow\Session\SessionInterface
	 * @Flow\Inject
	 */
	protected $session;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Index action
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('accounts', $this->accountRepository->findAll());
		$this->view->assign('originalIdentity', $this->persistenceManager->getObjectByIdentifier($this->session->getData('OriginalIdentity'), '\TYPO3\Flow\Security\Account'));
		$this->view->assign('impersonate', $this->persistenceManager->getObjectByIdentifier($this->session->getData('Impersonate'), '\TYPO3\Flow\Security\Account'));
	}

	/**
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return void
	 */
	public function impersonateAction($account) {
		$this->impersonateService->impersonate($account);
		$this->redirect('index');
	}

	/**
	 * @return void
	 */
	public function resetAction() {
		$this->impersonateService->undoImpersonate();
		$this->redirect('index');
	}
}

?>
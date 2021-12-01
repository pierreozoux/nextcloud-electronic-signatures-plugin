<?php

namespace OCA\ElectronicSignatures\Controller;

use OCA\ElectronicSignatures\Config;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use Psr\Log\LoggerInterface;

class SettingsApiController extends Controller {
    private $userId;

    /** @var IConfig */
    private $iConfig;

    /** @var Config */
    private $config;

    /** @var LoggerInterface */
    private $logger;

	public function __construct($AppName, IRequest $request, IConfig $iConfig, Config $config, LoggerInterface $logger, $UserId) {
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->iConfig = $iConfig;
		$this->config = $config;
		$this->logger = $logger;
	}

    /**
     * @NoAdminRequired
     */
	public function getSettings() {
        return new JSONResponse([
            'enable_otp' => $this->config->isOtpEnabled(),
            'enable_local' => $this->config->isSigningLocal(),
            'client_id_provided' => !empty($this->config->getClientId()),
            'secret_provided' => !empty($this->config->getSecret()),
        ]);
    }

    // TODO remove (deprecated).
    public function updateSettingsDepr() {
	    return $this->updateSettings();
    }

	public function updateSettings() {
	    try {
            // TODO actually check the inputs - attempt to get client config from eID Easy server.
            $clientId = $this->request->getParam('client_id', null);
            if ($clientId !== null) {
                // TODO get app name from some constant, here and elsewhere.
                $this->iConfig->setAppValue('electronicsignatures', 'client_id', $clientId);
            }

            $secret = $this->request->getParam('secret', null);
            if ($secret !== null) {
                $this->iConfig->setAppValue('electronicsignatures', 'secret', $secret);
            }

            $enableSandbox = $this->request->getParam('enable_sandbox', null);
            if ($enableSandbox !== null) {
                $this->iConfig->setAppValue('electronicsignatures', 'enable_sandbox', (int) (bool) $enableSandbox);
            }

            $enableOtp = $this->request->getParam('enable_otp', null);
            if ($enableOtp !== null) {
                $this->iConfig->setAppValue('electronicsignatures', 'enable_otp', (int) (bool) $enableOtp);
            }

            $enableLocalSigning = $this->request->getParam('enable_local_signing', null);
            if ($enableLocalSigning !== null) {
                $this->iConfig->setAppValue('electronicsignatures', 'enable_local_signing', (int) (bool) $enableLocalSigning);
            }

            $padesApiUrl = $this->request->getParam('pades_url', null);
            if ($padesApiUrl !== null) {
                $this->iConfig->setAppValue('electronicsignatures', 'pades_url', $padesApiUrl);
            }

            return new JSONResponse(['message' => 'Settings updated!']);
        } catch (\Throwable $e) {
            $this->logger->alert($e->getMessage() . "\n" . $e->getTraceAsString());
            return new JSONResponse(['message' => "Failed to update credentials: {$e->getMessage()}"], Http::STATUS_INTERNAL_SERVER_ERROR);
        }
	}
}

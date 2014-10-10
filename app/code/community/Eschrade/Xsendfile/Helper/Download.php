<?php

class Eschrade_Xsendfile_Helper_Download extends Mage_Downloadable_Helper_Download
{
	const WEBSERVER_APACHE = 'apache';
	const WEBSERVER_NGINX = 'nginx';
	const WEBSERVER_CUSTOM = 'custom';
	
	const WEBSERVER_HEADER_APACHE = 'X-SENDFILE';
	const WEBSERVER_HEADER_NGINX = 'X-Accel-Redirect';
	
	const CONFIG_ENABLED = 'system/sendfile/enabled';
	const CONFIG_WEBSERVER = 'system/sendfile/webserver';
	const CONFIG_BYPASS_MIME = 'system/sendfile/bypass_mime';
	const CONFIG_ALIAS_PATH = 'system/sendfile/alias_path';
	
	public function setResource($resourceFile, $linkType = self::LINK_TYPE_FILE)
	{
		$return = parent::setResource($resourceFile, $linkType);
		$enabled = Mage::getStoreConfigFlag(self::CONFIG_ENABLED);
		if ($enabled) {
			// We need to add the HTTP headers here because they have already been sent when output() is called
			$webServer = $this->getHttpRequest()->getServer('SERVER_SOFTWARE');
			$path = $this->_resourceFile;
			$header = null;
			if (stripos($webServer, self::WEBSERVER_APACHE) === 0) {
				$header = self::WEBSERVER_HEADER_APACHE;
			} else if (stripos($webServer, self::WEBSERVER_NGINX) === 0) {
				$header = self::WEBSERVER_HEADER_NGINX;
				$basePath = Mage::getConfig()->getBaseDir();
				if (strpos($path, $basePath) !== 0) {
					Mage::throwException('Download path does not match the base path. ');
				}
				$accelPath = Mage::getStoreConfig(self::CONFIG_ALIAS_PATH);
				$path = $accelPath . '/' . substr($path, strlen($basePath));
			}
				
			// Unsupported webserver
			if ($header === null) {
				Mage::log('Unsuccessful attempt to accelerate unsupported server');
			} else {
				$this->getHttpResponse()->setHeader($header, $path, true); // Adding true allows us to reset the header
			}
		}
		return $return;
	}
	
	public function getContentType()
	{
		$enabled = Mage::getStoreConfigFlag(self::CONFIG_ENABLED);
		if ($enabled) {
			if (Mage::getStoreConfigFlag(self::CONFIG_BYPASS_MIME)) {
				if ($this->_linkType == self::LINK_TYPE_FILE) {
					return Mage::helper('downloadable/file')->getFileType($this->_resourceFile);
				}
			}	
		}
		return parent::getContentType();
	}
	
	public function output()
	{
		$enabled = Mage::getStoreConfigFlag(self::CONFIG_ENABLED);
		if ($enabled) {
			return;
		}

		return parent::output();
	}
	
}
<?php

class Eschrade_Xsendfile_Model_System_Config_Source_Webserver 
{
	public function toOptionArray()
	{
		return array(
				array('value' => Eschrade_Xsendfile_Helper_Download::WEBSERVER_APACHE, 'label'=>Mage::helper('eschrade_xsendfile')->__('Apache')),
				array('value' => Eschrade_Xsendfile_Helper_Download::WEBSERVER_NGINX, 'label'=>Mage::helper('eschrade_xsendfile')->__('Nginx')),
		);
	}
}
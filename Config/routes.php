<?php

	Router::connect(
		'/img/viewVersion/*', 
		array(
			'plugin' => 'ApertureConnector', 
			'controller' => 'Img', 
			'action' => 'viewVersion'));
	
	
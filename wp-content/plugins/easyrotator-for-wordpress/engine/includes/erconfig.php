<?php

/*
Copyright 2011 DWUser.com.
Email contact: support {at] dwuser.com

This file is part of EasyRotator for WordPress.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


if (!class_exists('EasyRotatorPluginConfig'))
{

	/**
	 * Defines config items used generally.
	 */
	class EasyRotatorPluginConfig
	{
		public static $auth_key_optionName = 'easyrotator_auth_key0';
		
		// --- Storage config ---
		public static $contentDirWrapperName = 'EasyRotatorStorage'; // the dir that goes in the uploads/ dir
		public static $contentDirName = 'user-content'; // the user-content dir that goes in the wrapper dir

	}

}//end if not defined
?>
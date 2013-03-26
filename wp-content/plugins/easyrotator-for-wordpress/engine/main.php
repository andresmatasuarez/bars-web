<?php

/*
Copyright 2011-2012 DWUser.com.
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



// Load config class
require_once(dirname(__FILE__) . '/includes/erconfig.php');

// --- Load WP stuff if API is being called directly ---
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
	// we are in ./wp-content/plugins/easyrotator/engine (or similar), and want to get to ./wp-load.php
	$path = dirname(dirname(__FILE__));
	$found = false;
	for ($i=0; $i<10; $i++)
	{
		if (@file_exists($path . '/wp-load.php'))
		{
			$found = true;
			break;
		}
		$path = dirname($path);
	}
	if ($found)
		require_once($path . '/wp-load.php');
	else
		die('Unable to locate WordPress install.');
}


if ( !class_exists('EasyRotatorWP') ):
class EasyRotatorWP
{	
	function EasyRotatorWP()
	{
		// Handle api requests
		$this->handleAPIRequest();
		
		// Handle render requests (iframe, etc)
		$this->handleRenderRequests();
	}
	
	private function handleRenderRequests()
	{
		if (@$_GET['action'] == 'renderFrame')
		{
			$path = @$_GET['path'];
			?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Rotator iFrame Renderer</title>
<style type="text/css">html, body { margin:0; padding:0; }</style>
</head>

<body>
			<?php 
			echo($this->renderRotator($path));
			?>
</body>
</html>
			<?php
		}
	}
	
	/**
	 * Get directory listing based on path
	 * @param string $subpath
	 * @param bool $echo
	 * @return unknown_type
	 */
	public function getDirectoryListing($subpath, $echo=false)
	{
		$subpath = preg_replace('|/$|', '', $subpath); // strip off any trailing slash
		$path = $this->getContentDir() . $subpath;
		
		if ($subpath == '')
		{
			// Make sure that the root folder exists
			if (!file_exists($path))
				@mkdir($path);
		}
		
		// Make sure path exists
		if (!file_exists($path))
		{
			if ($echo)
				$this->outputAPIError('listDir', 'Specified path is invalid and doesn\'t exist.');
			else
				return false;
		}
		
		if ($handle = opendir($path))
		{
			// Get the labels for this path
			$pathParts = explode('/', $subpath);
			$friendlyPathParts = array();
			for ($i=0; strlen($subpath) > 0 && $i<count($pathParts); $i++)
			{
				$tempInfoPath = $this->getContentDir() . implode('/', array_slice($pathParts, 0, $i+1)) . '/info.txt';
				$info = @unserialize(file_get_contents($tempInfoPath));
				if (!is_array($info))
					$info = array('name'=>'(No name specified)');
				$friendlyPathParts[] = $info['name'];
			}
			$friendlyPath = implode('/', $friendlyPathParts);
			
			
			// Actually list the directory
			
			$folders = array();
			$rotators = array();
			
			while (false !== ($file = readdir($handle)))
			{
				$fileFull = $path . '/' . $file;
				if (is_dir($fileFull))
				{
					// We only want to look at dirs.  See if this is a rotator (prefixed by erc_)
					if (strpos($file, 'erc_') === 0)
					{
						// Read the info.txt file.
						$contents = file_get_contents($fileFull . '/info.txt');
						$info = unserialize($contents);
						$info['dirName'] = $file;
						$info['path'] = ($subpath != '' ? $subpath . '/' : '') . $file;
						$info['contentExists'] = file_exists($fileFull . '/content/content.html');
						
						$rotators[] = $info;
					}
					else if (preg_match('|^erf_|', $file)) //(!preg_match('|^.{1,2}$|', $file))
					{
						// Read the info.txt file. (contains the name)
						$contents = @file_get_contents($fileFull . '/info.txt');
						$info = unserialize($contents);
						if (!is_array($info))
						{
							$info = array('name'=>'(No name specified)');
							$this->writeFile($fileFull . '/info.txt', serialize($info));
						}
						$info['rawName'] = $file;
						$info['fullpath'] = ($subpath != '' ? $subpath . '/' : '') . $file;
						
						$folders[] = $info;
					}
				}
			}
			
			// Sort into alphabetical order
			usort($folders, array('EasyRotatorWP', 'sort_alphabetizeRotators'));
			
			// Sort the rotator listing
			usort($rotators, array('EasyRotatorWP', 'sort_alphabetizeRotators'));
			
			// Output everything
			if ($echo)
			{
				// Also include the maximum upload size; it's put here so the program can warn the user about overly-large uploads.
				$maxUploadSize = EasyRotatorWPUtils::getMaxUploadSize();
			
				$this->startAPIResponse();
				echo('<response status="success">');
					echo('<listing path="' . $this->escapeXMLAttr($subpath) . '" friendlyPath="' . $this->escapeXMLAttr($friendlyPath) . '" maxUploadSize="' . $maxUploadSize . '">');
						foreach ($folders as $folder)
						{
							echo('<folder ' . $this->getArrayAsXMLAttributes($folder) . ' />');
							// old: echo('<folder name="' . $this->escapeXMLAttr($folder) . '" fullpath="' . $this->escapeXMLAttr(($subpath != '' ? $subpath . '/' : '') . $folder) . '" />');
						}
						foreach ($rotators as $rotator)
						{
							echo('<rotator ' . $this->getArrayAsXMLAttributes($rotator) . ' />');
						}
					echo('</listing>');
				echo('</response>');
			}
			else
			{
				return array('rotators'=>$rotators, 'folders'=>$folders);
			}
		}
		else
		{
			if ($echo)
				$this->outputAPIError('listDir', 'Unable to access specified directory.');
			else
				return false;
		}
	}
	
	/**
	 * Given an array of rotator paths, returns the corresponding rotator names in an array keyed by
	 * the paths.  The value is FALSE if a name can't be found.
	 * @param array $paths Array of rotator paths
	 * @return array Array of rotator names keyed by corresponding path
	 **/
	public function getRotatorNames($paths)
	{
		$ret = array();
		
		foreach ($paths as $path)
		{
			$sentPath = $path;
			
			$path = $this->getContentDir() . $path;
			$path = preg_replace('|/$|', '', $path); // remove any trailing slash
			$infoPath = $path . '/info.txt';
				
			// Validate
			if (!preg_match('|/erc_[^/]+$|', $path) || !file_exists($path) || !is_dir($path))
			{
				$ret[$sentPath] = false;
				continue;
			}
				
			// Read the config file, update name, then rewrite it
			$contents = file_get_contents($infoPath);
			$info = unserialize($contents);
			
			$ret[$sentPath] = $info['name'];
		}
		
		return $ret;
	}
	
	private function handleAPIRequest()
	{
		if (@$_GET['action'] == 'api')
		{
			
			// Authenticate call.
			if (!$this->authenticateUser($this->getParam('key')))
			{
				// Error -- api key specified
				$this->outputAPIError('auth', 'Invalid API credentials specified.');
				return;
			}

            // Now that we're authenticated, enable error output to aid debugging
            ini_set('display_errors', '1');

			
			// Determine method and process
			
			$method = $this->getParam('method');
			if ($method == 'StorageAdmin.listDir')
			{
				// ---------------------------------------------
				// --- METHOD: StorageAdmin.listDir
				//			Lists dir based on passed path parameter.
				// ---------------------------------------------
				
				$subpath = $this->getParam('path');
				$this->getDirectoryListing($subpath, true); // output=true
				
				// ---------------------------------------------
				// ---------------------------------------------
			} 
			else if ($method == 'StorageAdmin.createDir')
			{
				// ---------------------------------------------
				// --- METHOD: StorageAdmin.createDir
				// 		Params: path (parent path, no slash), folderName
				// ---------------------------------------------
				
				$path = $this->getParam('path');
				$folderName = $this->getParam('folderName');
				
				// Remove any trailing slash on the path, setup & validate
				$path = preg_replace('|/$|', '', $path);
				$folderNameRaw = 'erf_' . rand(0,99) . '_' . time();
				$fullDest = $this->getContentDir() . $path;
				$fullPath = $fullDest . '/' . $folderNameRaw;

				if (/*$path == '' || */$folderName == '') // can be in root, but must have a folder name
				{
					$this->outputAPIError('params', 'Invalid or missing parameter.');
					return;
				}
				elseif (!file_exists($fullDest) || !is_dir($fullDest))
				{
					$this->outputAPIError('params', 'Specified parent path doesn\'t exist or is invalid.');
					return;
				}
				elseif (!is_writable($fullDest) && !@chmod($fullDest, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				// note: not validating if folder name is already being used
				
				// Attempt to create dir
				if (@mkdir($fullPath))
				{
					// Write the info.txt file
					$info = array('name'=>$folderName);
					$this->writeFile($fullPath . '/info.txt', serialize($info));
					
					$this->startAPIResponse();
					echo('<result status="success" message="The new folder has successfully been created." />');
				}
				else
				{
					$this->outputAPIError('createDir', 'An error occurred while attempting to create the requested folder.  Creation failed.');
				}
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'StorageAdmin.removeDir')
			{
				// ---------------------------------------------
				// --- METHOD: StorageAdmin.removeDir
				//		Params: path (full path to folder, no slash)
				// ---------------------------------------------
				
				$path = $this->getParam('path');
				
				$path = $this->getContentDir() . $path;
				$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				
				// Make sure it exists
				if (!file_exists($path))
				{
					$this->outputAPIError('params', 'Specified folder doesn\'t exist.  Please double-check the specified values.');
					return;
				}
				elseif (!is_writable($path) && !@chmod($path, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				
				// Attempt to remove dir
				if ($this->rrmdir($path))
				{
					$this->startAPIResponse();
					echo('<result status="success" message="The specified folder has successfully been removed." />');
				}
				else
				{
					$this->outputAPIError('removeDir', 'An error occurred while attempting to remove the specified folder.  Deletion did not fully complete.');
				}
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'StorageAdmin.renameDir')
			{
				// ---------------------------------------------
				// --- METHOD: StorageAdmin.renameDir
				//		Params: path (full path to folder, no slash), newName
				// ---------------------------------------------
				
				$path = $this->getParam('path');
				$newName = $this->getParam('newName');
				
				$path = $this->getContentDir() . $path;
				$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				$infoPath = $path . '/info.txt';
				
				// Make sure old exists
				if (!file_exists($path) || !is_dir($path))
				{
					$this->outputAPIError('params', 'The specified path doesn\'t exist or isn\'t a folder.  Please double-check the specified values.');
					return;
				}
				elseif (!is_writable($infoPath) && !@chmod($infoPath, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				if ($newName == '')
				{
					$this->outputAPIError('params', 'No new name was specified.  Double-check the specified name.');
					return;
				}
				
				// Note: not checking to see if this folder is already being used.
				
				// Attempt to update info file, renaming it
				$info = @unserialize(file_get_contents($infoPath));
				if (!is_array($info))
					$info = array();
				$info['name'] = $newName;
				$this->writeFile($infoPath, serialize($info));
				if (true)
				{
					$this->startAPIResponse();
					echo('<result status="success" message="The specified folder has successfully been renamed." />');
				}
				else
				{
					$this->outputAPIError('renameDir', 'An error occurred while attempting to rename the specified folder.  Renaming did not fully complete.');
				}
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'Rotator.createAndCheckOut')
			{
				// ---------------------------------------------
				// --- METHOD: Rotator.createAndCheckOut
				//    Params: path, name, description, machineID (to record checkout), checkOutID
				// ---------------------------------------------
				
				$path = $this->getParam('path');
				$name = $this->getParam('name');
				$desc = $this->getParam('description');
				$machineID = $this->getParam('machineID');
				$checkOutID = $this->getParam('checkOutID');
				
				$path = $this->getContentDir() . $path;
				$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				
				// Validate
				if (!file_exists($path) || !is_dir($path))
				{
					$this->outputAPIError('params', 'The specified path doesn\'t exist or isn\'t a folder.  Please double-check the specified values.');
					return;
				}
				elseif (!is_writable($path) && !@chmod($path, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				if ($name == '')
				{
					$this->outputAPIError('params', 'No name was specified for the new rotator.  Double-check the specified parameters.');
					return;
				}
				
				
				// Append the new folder path, in format erc_NN_timestamp
				$dirName = 'erc_' . rand(0,99) . '_' . time();
				$path .= '/' . $dirName;
				
				// Create the folder, then the content/ subfolder, then content/exists.txt flag file
				if (!@mkdir($path))
				{
					$this->outputAPIError('createDir', 'Unable to create directory.  Rotator creation failed.');
					return;
				}
				if (!@mkdir($path . '/content'))
				{
					$this->outputAPIError('createDir', 'Unable to create content sub-directory.  Rotator creation failed.');
					return;
				}
				$this->writeFile($path . '/content/exists.txt', 'yes'); // this prevents the zipper from choking
				
				// Create the info.txt file
				$info = array('name'=>$name, 'description'=>$desc, 'isCheckedOut'=>true, 'checkedOutMachineID'=>$machineID, 'checkedOutTime'=>time(), 'checkOutID'=>$checkOutID);
				$infoPath = $path . '/info.txt';
				$this->writeFile($infoPath, serialize($info));

				// Return success
				$this->startAPIResponse();
				echo('<result status="success" message="The new rotator has successfully been created." dirName="' . $this->escapeXMLAttr($dirName) . '" />');
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'Rotator.remove')
			{
				// ---------------------------------------------
				// --- METHOD: Rotator.remove
				//		Params: path
				// ---------------------------------------------
				
				$path = $this->getParam('path');
				
				$path = $this->getContentDir() . $path;
				$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				
				// Validate
				if (!preg_match('|/erc_[^/]+$|', $path) || !file_exists($path) || !is_dir($path))
				{
					$this->outputAPIError('params', 'The specified path isn\'t a valid rotator path.  Please double-check the specified values.');
					return;
				}
				elseif (!is_writable($path) && !@chmod($path, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				
				// Attempt to remove entire dir
				if ($this->rrmdir($path))
				{
					$this->startAPIResponse();
					echo('<result status="success" message="The specified rotator has successfully been removed." />');
				}
				else
				{
					$this->outputAPIError('removeRotator', 'An error occurred while attempting to remove the rotator.  Deletion did not fully complete.');
				}
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'Rotator.rename')
			{
				// ---------------------------------------------
				// --- METHOD: Rotator.rename
				//		Params: path, newName
				// ---------------------------------------------
				
				$path = $this->getParam('path');
				$newName = $this->getParam('newName');
				
				$path = $this->getContentDir() . $path;
				$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				$infoPath = $path . '/info.txt';
				
				// Validate
				if (!preg_match('|/erc_[^/]+$|', $path) || !file_exists($path) || !is_dir($path))
				{
					$this->outputAPIError('params', 'The specified path isn\'t a valid rotator path.  Please double-check the specified values.');
					return;
				}
				elseif (!is_writable($infoPath) && !@chmod($infoPath, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				
				// Read the config file, update name, then rewrite it
				$contents = file_get_contents($infoPath);
				$info = unserialize($contents);
				
				$info['name'] = $newName;
				
				$this->writeFile($infoPath, serialize($info));
				
				// Return success
				$this->startAPIResponse();
				echo('<result status="success" message="The rotator has successfully been renamed." />');
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'Rotator.checkOut')
			{
				// ---------------------------------------------
				// --- METHOD: Rotator.checkOut
				//		Params: path, machineID, checkOutID
				// ---------------------------------------------
				
				$subpath = $this->getParam('path');
				$machineID = $this->getParam('machineID');
				$checkOutID = $this->getParam('checkOutID');
				
				$path = $this->getContentDir() . $subpath;
				$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				$infoPath = $path . '/info.txt';
				
				// Validate
				if (!preg_match('|/erc_[^/]+$|', $path) || !file_exists($path) || !is_dir($path))
				{
					$this->outputAPIError('params', 'The specified path isn\'t a valid rotator path.  Please double-check the specified values.');
					return;
				}
				elseif (!is_writable($path) && !@chmod($path, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				elseif (!is_writable($infoPath) && !@chmod($infoPath, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				
				// Read the config file, update checkout info, then rewrite it
				$contents = file_get_contents($infoPath);
				$info = unserialize($contents);
				
				$wasCheckedOutByOther = $info['isCheckedOut'] && $info['checkedOutMachineID'] != $machineID;
				$info['isCheckedOut'] = true;
				$info['checkedOutMachineID'] = $machineID;
				$info['checkedOutTime'] = time();
				$info['checkOutID'] = $checkOutID;
				
				$this->writeFile($infoPath, serialize($info));
				
				// Zip up the current contents (remove any existing content_NNNN zips), and prepare it for download
				$zipName = EasyRotatorWPUtils::prepareContentsZIP($path);
				$zipPath = 'deprecated'; //$this->_contentDirName . '/' . $subpath . '/' . $zipName;
				$zipURL = $this->getContentDirURL() . $subpath . '/' . $zipName;
				
				// Return success
				$this->startAPIResponse();
				echo('<result status="success" message="The rotator has successfully been checked out." justSeized="' . ($wasCheckedOutByOther ? 'true' : 'false') . '" zipPath="' . $zipPath . '" zipURL="' . $zipURL . '" />');
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'Rotator.checkIn')
			{
				// ---------------------------------------------
				// --- METHOD: Rotator.checkIn
				//		Params: path, machineID, checkInID, [file=Filedata] the zip file.
				// ---------------------------------------------
				
				// Increase time limit since we'll be waiting for upload to come
				@ini_set('max_execution_time', 420); // 7 min
				
				$subpath = $this->getParam('path');
				$machineID = $this->getParam('machineID');
				$checkInID = $this->getParam('checkInID');
				
				$path = $this->getContentDir() . $subpath;
				$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				$infoPath = $path . '/info.txt';
				
				// Validate
				if (!preg_match('|/erc_[^/]+$|', $path) || !file_exists($path) || !is_dir($path))
				{
					$this->outputAPIError('params', 'The specified path isn\'t a valid rotator path.  Please double-check the specified values.');
					return;
				}
				elseif (!is_writable($path) && !@chmod($path, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				elseif (!is_writable($path . '/content') && !@chmod($path . '/content', 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				elseif (!is_writable($infoPath) && !@chmod($infoPath, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				
				// Read the config file, validate, etc
				$contents = file_get_contents($infoPath);
				$info = unserialize($contents);
				
				// See if content exists; if not, then this was the first checkin.
				$isFirstCheckIn = !file_exists($path . '/content/content.html');
				
				// Make sure this user has the authority to check this in (someone else hasn't seized it)
				if ($info['checkedOutMachineID'] != $machineID)
				{
					$this->outputAPIError('permissions', 'This rotator appears to have been checked-out by force (seized) by another user.  You cannot check it in.  You must close it without checking in any changes, then wait until the other user releases it, or seize it by force yourself (not recommended!).');
					return;
				}
				
				// Attempt to extract the uploaded ZIP file
				$uploadSuccess = EasyRotatorWPUtils::processUploadedZIP($path . '/content');
				if (!$uploadSuccess['success'])
				{
					$this->outputAPIError('upload', 'Upload failed: ' . $uploadSuccess['message']);
					return;
				}
				
				// Update and save the config values
				$info['isCheckedOut'] = false;
				$info['checkedInTime'] = time();
				$info['checkedInMachineID'] = $machineID; // just for good measure
				$info['checkInID'] = $checkInID;
				$this->writeFile($infoPath, serialize($info));
				
				// Return success
				$this->startAPIResponse();
				$successPath = $subpath; //$this->_contentDirName . '/' . $subpath;
				echo('<result status="success" message="The rotator has successfully been checked in." successID="' . $successPath . '" isFirstCheckIn="' . ($isFirstCheckIn ? 'true' : 'false') . '" />');
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'Rotator.cancelCheckOut')
			{
				// ---------------------------------------------
				// --- METHOD: Rotator.cancelCheckOut
				//		Params: path, machineID
				// ---------------------------------------------
				
				$path = $this->getParam('path');
				$machineID = $this->getParam('machineID');
				
				$path = $this->getContentDir() . $path;
				$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				$infoPath = $path . '/info.txt';
				
				// Validate
				if (!preg_match('|/erc_[^/]+$|', $path) || !file_exists($path) || !is_dir($path))
				{
					$this->outputAPIError('params', 'The specified path isn\'t a valid rotator path.  Please double-check the specified values.');
					return;
				}
				elseif (!is_writable($infoPath) && !@chmod($infoPath, 0777))
				{
					$this->outputAPIError('config', 'Specified location is not writeable.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
					return;
				}
				
				// Read the config file, validate, etc
				$contents = file_get_contents($infoPath);
				$info = unserialize($contents);
				
				// Make sure this user has the authority to check this in (someone else hasn't seized it)
				$hasAuthority = true;
				if ($info['checkedOutMachineID'] != $machineID)
				{
					//$this->outputAPIError('permissions', 'This rotator appears to have been checked-out by force (seized) by another user.  You cannot check it in.  You must close it without checking in any changes, then wait until the other user releases it, or seize it by force yourself (not recommended!).');
					//return;
					$hasAuthority = false;
				}
				else
				{
					// Update and save the config values
					$info['isCheckedOut'] = false;
					$info['checkedInTime'] = time(); // not an official param; just adding for good measure.
					$this->writeFile($infoPath, serialize($info));
				}
				
				// Return success
				$this->startAPIResponse();
				echo('<result status="success" message="' . ($hasAuthority ? 'Rotator has successfully been checked back in.' : 'The checkout of this rotator has successfully been canceled.  While you had the rotator checked out, someone else seized control of it and checked it out; they currently have control over it.') . '" />');
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
            else if ($method == 'WordPress.getMediaLibraryPhotos')
            {
                // ---------------------------------------------
                // --- METHOD: WordPress.getMediaLibraryPhotos
                //			Params: limit, offset, postParent, search, year, month
                //			Desc: Used by the wizard to provide search for photos in the media library (or in a post's gallery)
                // ---------------------------------------------

                /* props:
                        limit: (int) max number of photos to return
                        offset: (int) where to start the return
                        postParent: (int) the ID of the parent post if we're looking in a gallery
                        search: (str) filter by search string; '' -> no filter
                        year: (int) filter by year; 0 -> no filter
                        month: (int) filter by month; 0 -> no filter
                    */

                // Get params
                $limit = intval( $this->getParam('limit') );
                $offset = intval( $this->getParam('offset') );
                $postParent = $this->getParam('postParent');
                if ($postParent == '')
                    $postParent = -1;
                else
                    $postParent = intval($postParent);
                $search = $this->getParam('search');
                $year = intval( $this->getParam('year') );
                $month = intval( $this->getParam('month') );

                // Use the getLibraryPhotoInfo method
                $params = array(
                    'limit' => $limit,
                    'offset' => $offset,
                    'postParent' => $postParent,
                    'search' => $search,
                    'year' => $year,
                    'month' => $month,
                );

                $info = self::getLibraryPhotoInfo( $params ); // format: array('photos' => array( array('thumb_id', 'src', 'thumb', 'title', 'description') , ...) Array of all photos, 'photos_found' => total number of results for this search )
                $photosFound = $info['photos_found'];
                $photos = $info['photos'];


                // Format output and return
                $this->startAPIResponse();
                echo('<result status="success" photosFound="' . $photosFound . '">');
                foreach ($photos as $photo)
                {
                    echo('<photo thumb_id="' . htmlspecialchars($photo['thumb_id']) . '" src="' . htmlspecialchars($photo['src']) . '" thumb="' . htmlspecialchars($photo['thumb']) . '" title="' . htmlspecialchars($photo['title']) . '" description="' . htmlspecialchars($photo['description']) . '" />');
                }
                echo('</result>');



                // ---------------------------------------------
                // ---------------------------------------------
            }
			else if ($method == 'WordPress.getFeaturedPhotos')
			{
				// ---------------------------------------------
				// --- METHOD: WordPress.getFeaturedPhotos
				//			Params: filter, filterDetail, limit, order, excludeCurrent, fullSize, thumbSize
				//			Desc: Used by the wizard to provide previews of photos to be added when adding dynamic featured-photo data.
				// ---------------------------------------------
				
				/* props:
					data-filter: recent | tags | cats
						data-filterDetail (only applies if tags or cats for above prop): all:12,24,26  or any:12,24,26    using tag IDs or cat IDs
					data-limit: (int) max number of photos to return; 0 = unlimited
					data-order: rand | desc [default] | asc
				    data-excludeCurrent: true | false [default]
				    data-fullSize: 'full' [default] | 'large' | 'medium' | 'thumbnail' | '320,240' (pixels)
				    data-thumbSize: 'full' | 'large' | 'medium' | 'thumbnail' [default] | '320,240' (pixels)
				*/
				
				// Get params
				$filter = $this->getParam('filter');
				$filterDetail = $this->getParam('filterDetail');
				$limit = intval($this->getParam('limit'));
				$order = $this->getParam('order');
                $excludeCurrent = $this->getParam('excludeCurrent');
                $fullSize = $this->getParam('fullSize');
                $thumbSize = $this->getParam('thumbSize');
				
				// Use the getFeaturedPhotoInfo method...
				$params = array(
					'data-filter' => $filter,
					'data-filterDetail' => $filterDetail,
					'data-limit' => $limit,
					'data-order' => $order,
                    'data-excludeCurrent' => $excludeCurrent,
				);
                if (strlen($fullSize) > 0)
                    $params['data-fullSize'] = $fullSize;
                if (strlen($thumbSize) > 0)
                    $params['data-thumbSize'] = $thumbSize;
				$photos = self::getFeaturedPhotoInfo($params); // format: array( array('post_id', 'thumb_id', 'src', 'thumb', 'title', 'description', 'link') , ...)
				
				// Format output and return
				$this->startAPIResponse();
				echo('<result status="success" photoCount="' . count($photos) . '">');
				foreach ($photos as $photo)
				{
					echo('<photo post_id="' . htmlspecialchars($photo['post_id']) . '" thumb_id="' . htmlspecialchars($photo['thumb_id']) . '" src="' . htmlspecialchars($photo['src']) . '" thumb="' . htmlspecialchars($photo['thumb']) . '" title="' . htmlspecialchars($photo['title']) . '" description="' . htmlspecialchars($photo['description']) . '" link="' . htmlspecialchars($photo['link']) . '" />');
				}
				echo('</result>');
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'WordPress.replaceDynamicHTML')
			{
				// ---------------------------------------------
				// --- METHOD: WordPress.replaceDynamicHTML
				//			Params: html
				//			Desc: Used by the wizard when previewing a rotator that includes dynamic HTML.  In these cases, replaces the appropriate HTML.
				// ---------------------------------------------
				
				$html = $this->getParam('html');
                if (get_magic_quotes_gpc())
                    $html = stripslashes($html);
				$htmlUpdated = self::replaceDynamicRotatorHTML($html);

				$this->startAPIResponse();
				echo('<result status="success" message="Live HTML may be found in the htmlUpdated attribute." htmlUpdated="' . htmlspecialchars($htmlUpdated) . '" />');
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'WordPress.getTags')
			{
				// ---------------------------------------------
				// --- METHOD: WordPress.getTags
				//			Desc: Used by the wizard to provide a list of available tags for dynamic featured-photos data sources
				// ---------------------------------------------
				
				$args = array(
					'orderby' => 'name', //'count',
					'order' => 'ASC', //'DESC',
				);
				$tags = get_tags($args);
				
				$this->startAPIResponse();
				
				echo('<result status="success" numTags="' . count($tags) . '">');
				foreach ($tags as $tag)
				{
					echo('<tag id="' . $tag->term_id . '" name="' . htmlspecialchars( $tag->name ) . '" />');
				}
				echo('</result>');
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else if ($method == 'WordPress.getCats')
			{
				// ---------------------------------------------
				// --- METHOD: WordPress.getCats
				//			Desc: Used by the wizard to provide a list of available categories for dynamic featured-photos data sources
				// ---------------------------------------------
				
				$args = array(
					'orderby' => 'name', //'count',
					'order' => 'ASC', //'DESC',
				);
				$cats = get_categories($args);
				
				$this->startAPIResponse();
				
				echo('<result status="success" numCats="' . count($cats) . '">');
				foreach ($cats as $cat)
				{
					$topLevel = intval( $cat->category_parent ) == 0;
					echo('<cat id="' . $cat->cat_ID . '" name="' . htmlspecialchars( $cat->cat_name ) . '" topLevel="' . ($topLevel ? 'true' : 'false') . '" />');
				}
				echo('</result>');
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
            else if ($method == 'WordPress.getRecentPostsAndPages')
            {
                // ---------------------------------------------
                // --- METHOD: WordPress.getRecentPostsAndPages
                //          Params: search, searchIn, limit
                //			Desc: Used by the wizard to provide a list of recent (or searched) posts/pages to filter getMediaLibraryPhotos
                // ---------------------------------------------

                /* props:
                    search: optional search term
                        searchIn: if search is specified, sets whether we're looking for a post or a page
                    limit: max num results; default=5 of each type
                */

                $search = $this->getParam('search');
                $searchIn = $this->getParam('searchIn');
                if ($searchIn == '' || $searchIn == 'both')
                    $searchIn = 'both';
                else
                    $searchIn = ($searchIn == 'pages') ? 'pages' : 'posts';
                $limit = intval( $this->getParam('limit') );
                if ($limit < 1)
                    $limit = 5;


                // Create output holders
                $outputPosts = '';
                $outputPages = '';

                // Get the recent posts, pages
                $args = array(
                    'post_type' => array('post'),
                    'posts_per_page' => $limit,
                    'post_status' => 'any',
                    'orderby' => 'date',
                    'order' => 'DESC',
                );
                if ($search != '')
                    $args['s'] = $search;

                if ($searchIn == 'both' || $searchIn == 'posts')
                {
                    $query = new WP_Query();
                    $query->query( $args );
                    foreach ( $query->posts as $post )
                    {
                        $outputPosts .= '<item id="' . $post->ID . '"
                                               title="' . htmlspecialchars($post->post_title) . '"
                                               status="' . htmlspecialchars($post->post_status) . '"
                                               date="' . htmlspecialchars($post->post_date) . '"
                                               ts="' . strtotime($post->post_date) . '" />' . "\n";
                    }
                }
                if ($searchIn == 'both' || $searchIn == 'pages')
                {
                    $args['post_type'] = array('page');
                    $query = new WP_Query();
                    $query->query( $args );
                    foreach ( $query->posts as $post )
                    {
                        $outputPages .= '<item id="' . $post->ID . '"
                                               title="' . htmlspecialchars($post->post_title) . '"
                                               status="' . htmlspecialchars($post->post_status) . '"
                                               date="' . htmlspecialchars($post->post_date) . '"
                                               ts="' . strtotime($post->post_date) . '" />' . "\n";
                    }
                }


                // Write out response
                $this->startAPIResponse();

                echo('<result status="success">');
                echo( '<posts>' . $outputPosts . '</posts>' );
                echo( '<pages>' . $outputPages . '</pages>' );
                echo('</result>');


                // ---------------------------------------------
                // ---------------------------------------------
            }
			else if ($method == 'StorageAdmin.fakeBla')
			{
				// ---------------------------------------------
				// --- METHOD: StorageAdmin.check
				// ---------------------------------------------
				
				
				
				
				// ---------------------------------------------
				// ---------------------------------------------
			}
			else
			{
				// Error -- invalid method specified
				$this->outputAPIError('request', 'Invalid method specified.');
			}
		}
	}
	
	// OUTPUT UTILS
	
	private function startAPIResponse()
	{
		header('Content-Type: text/xml');
		echo('<?xml version="1.0" encoding="UTF-8"?' . '>');
	}
	
	private function outputAPIError($loc, $message, $exit=true)
	{
		$this->startAPIResponse();
		// demo: echo('<response status="error" loc="request" message="Invalid method specified." />');
		echo('<response status="error" loc="' . $loc . '" message="' . $message . '" />');
		if ($exit)
			exit();
	}
	
	static function getArrayAsXMLAttributes($array)
	{
		$r = '';
		foreach ($array as $key=>$value)
		{
			$r .= ' ' . $key . '="';
			if ($value === true || $value === false)
			{
				$r .= ($value ? 'true' : 'false');
			}
			else
			{
				$r .= self::escapeXMLAttr($value);
			}
			$r .= '"';
		}
		return $r;
	}
	
	static function escapeXMLAttr($val)
	{
		$val = str_replace('&', '&amp;', $val);
		$val = str_replace('"', '&quot;', $val);
		$val = str_replace('>', '&gt;', $val);
		$val = str_replace('<', '&lt;', $val);
		return $val;
	}

	static function sort_alphabetizeRotators($a, $b)
	{
		/*if ($a['name'] == $b['name'])
			return 0;
		return ($a['name'] < $b['name']) ? -1 : 1;*/
		return strcasecmp($a['name'], $b['name']);
	}
	
	/*static function rrmdir($dir)
	{ 
		if (is_dir($dir))
		{ 
			$objects = scandir($dir); 
			foreach ($objects as $object)
			{ 
				if ($object != "." && $object != "..")
				{ 
					if (filetype($dir."/".$object) == "dir")
						rrmdir($dir."/".$object);
					else 
						unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}*/

	// Recursive remove directory
	static function rrmdir($directory, $empty=FALSE)
	{
		if (substr($directory, -1) == '/')
		{
			$directory = substr($directory, 0, -1);
		}
		if (!file_exists($directory) || !is_dir($directory))
		{
			return false;
		}
		elseif(is_readable($directory))
		{
			$handle = opendir($directory);
			while (false !== ($item = readdir($handle)))
			{
				if ($item != '.' && $item != '..')
				{
					$path = $directory . '/' . $item; 
					if (is_dir($path)) 
					{
						self::rrmdir($path);
						//if (!self::rrmdir($path))
						//	return false;
					}
					else
					{
						unlink($path);
					}
				}
			}
			closedir($handle);
			if ($empty == false)
			{
				if (!rmdir($directory))
				{
					return false;
				}
			}
		}
		return true;
	}
	// ------------------------------------------------------------
	
	static function writeFile($filePath, $content)
	{
		if (false === ($fh = fopen($filePath, 'w')))
			return false;
		fwrite($fh, $content);
		fclose($fh);
		return true;
	}
	
	
	// ----- RENDER UTILS ------
	
	/**
	 * Passed the full path to a rotator, outputs the full HTML for the rotator.
	 * @param string $fullPath For example, erf_1434/erc_1545235
	 * @return void
	 */
	public function renderRotator($fullPath)
	{
		$path = $this->getContentDir() . $fullPath;
		$path = preg_replace('|/$|', '', $path); // remove any trailing slash
				
		// Validate
		if (!preg_match('|/erc_[^/]+$|', $path) || !file_exists($path) || !is_dir($path))
		{
			return ('<div style="background:#000; padding: 10px; color: #FFF;">Invalid rotator ID specified (path <em>' . $fullPath . '</em> doesn\'t exist).  Unable to display rotator.</div>');
		}
		
		// Make sure that content exists
		$contentDir = $path . '/content/';
		$contentFile = $contentDir . 'content.html';
		if (!file_exists($contentFile))
		{
			return ('<div style="background:#000; padding: 10px; color: #FFF;">Invalid rotator ID specified (content for path <em>' . $fullPath . '</em> doesn\'t exist yet).  Unable to display rotator.</div>');
		}
		
		// Read the file
		$content = file_get_contents($contentFile);

        // Update SSL if needed
        if (is_ssl())
        {
            $content = str_replace('http://c520866.r66.cf2.rackcdn.com/', 'https://c520866.ssl.cf2.rackcdn.com/', $content);
            $content = str_replace('http://easyrotator.s3.amazonaws.com/', 'https://easyrotator.s3.amazonaws.com/', $content);
        }
		
		// --- Update all images in the content section to use the full path ---
		$urlHere = $this->getContentDirURL() . $fullPath . '/content/';
        if (is_ssl())
            $urlHere = preg_replace('|^http://|', 'https://', $urlHere);
		
		// Find the content
		$s = $content;
		$index = $this->strpos_preg($s, '|<div[^>]+?data-ertype="content"[^>]*>|i');
		$index2 = strpos($s, '>', $index) + 1;
		$closePos = $this->findCorrespondingEndingTag($s, $index, 'div');
		
		$beforeMicro = $this->substring($s, 0, $index);
		$before = $this->substring($s, 0, $index2);
		$contentDiv = $this->substring($s, $index2, $closePos['closeStart']);
		$after = substr($s, $closePos['closeStart']);
		
		// Search all of the photos.
		$contentDiv = preg_replace_callback(
			'|(<img.*? src=")([^"]*)(")|',
			create_function(
				'$matches',
				'
				$src = $matches[2];
				if (!preg_match(\'|^[a-z]+://|i\', $src) && !preg_match(\'|^/|\', $src)) // check for protocol, site-relative
					return ($matches[1] . \'' . $urlHere . '\' . $matches[2] . $matches[3]);
				return $matches[0];
				'
			),
			$contentDiv
		);
		
		// Search for audio and video.  ... should be able to use (eraudio|ervideo), but this doesn't work.
		self::$renderRotator_avReplaceCallback_urlHere = $urlHere; // set the replace value
		$contentDiv = preg_replace_callback(
			'|<span[^>]+?class="eraudio[\s"][^>]*>|i',
			'EasyRotatorWP::renderRotator_avReplaceCallback',
			$contentDiv
		);
		$contentDiv = preg_replace_callback(
			'|<span[^>]+?class="ervideo[\s"][^>]*>|i',
			'EasyRotatorWP::renderRotator_avReplaceCallback',
			$contentDiv
		);
        // Replace background audio
        $before = preg_replace_callback(
            '|(data-erAudioConfig="\{.*?src:\')([^\']*)(\'.*?\}")|i',
            create_function(
                '$matches',
                '
				$src = $matches[2];
				if (!preg_match(\'|^[a-z]+://|i\', $src)) // check for protocol
					return ($matches[1] . \'' . $urlHere . '\' . $matches[2] . $matches[3]);
				return $matches[0];
				'
            ),
            $before
        );
		
		// Search for dynamic content and replace
		$contentDiv = self::replaceDynamicRotatorHTML($contentDiv);
		
		
		// Reassemble updated content.
		$content = ($before . $contentDiv . $after);
		
		if (preg_match('|<div[^>]+data-erConfig="[^"]+autoplayDelta:\s?' . (cos(0)*5*500) . '[^"]+"[^>]*>|i', $beforeMicro, $matches))
		{		
			$index = $this->strpos_preg($content, '|<div[^>]+?class="erabout["\s][^>]*>|i');
			$index2 = strpos($content, '>', $index) + 1;
			$closePos = $this->findCorrespondingEndingTag($content, $index, 'div');
		
			$before = $this->substring($content, 0, $index2);
			//$inside = $this->substring($content, $index2, $closePos['closeStart']);
			$after = substr($content, $closePos['closeStart']);
			
			$content = $before . $after;
			
			// Strip out <noscript> too fully (not just inner content)
			$index = $this->strpos_preg($content, '|<noscript>|i');
			$index2 = strpos($content, '>', $index) + 1;
			$closePos = $this->findCorrespondingEndingTag($content, $index, 'noscript');
		
			$before = $this->substring($content, 0, $index); //$this->substring($content, 0, $index2);
			//$inside = $this->substring($content, $index2, $closePos['closeStart']);
			$after = substr($content, $closePos['closeEnd']); //substr($content, $closePos['closeStart']);
			
			$content = $before . $after;
		}
		
		return $content;
	}
	
	private static $renderRotator_avReplaceCallback_urlHere = '';
	private static function renderRotator_avReplaceCallback($matches)
	{
		$node = $matches[0];
		return preg_replace_callback(
			'|(data-erConfig="\{.*?src:\')([^\']*)(\'.*?\}")|i',
			create_function(
				'$matches',
				'
				$src = $matches[2];
				if (!preg_match(\'|^[a-z]+://|i\', $src)) // check for protocol
					return ($matches[1] . \'' . self::$renderRotator_avReplaceCallback_urlHere . '\' . $matches[2] . $matches[3]);
				return $matches[0];
				'
			),
			$node
		);
	}
	
	// A separate utility method, so it can easily be used by the public API too.
	private static function replaceDynamicRotatorHTML($contentDiv)
	{
		return preg_replace_callback(
			'|<li\s+class="erwpFeaturedPhotos"\s+(.*?)\s*>.*?</li>|si',
			'EasyRotatorWP::replaceDynamicRotatorHTML_dynamicReplaceCallback',
			$contentDiv
		);
	}
	
	private static function replaceDynamicRotatorHTML_dynamicReplaceCallback($matches) // replaces dynamic <li> calls for featured photos.
	{
		$propsCode = $matches[1]; // the portion with: data-bla="bla" data-bla2="bla2" ...
		preg_match_all('|\b([0-9A-Z-_]+)="([^"]*)"|i', $propsCode, $submatches, PREG_SET_ORDER);
	
		$props = array();
		foreach ($submatches as $match)
		{
			$props[ $match[1] ] = $match[2];
		}
		
		// Get the featured photo dynamic HTML, return it
		$html = self::getFeaturedPhotoDynamicHTML( $props );
		return $html;
	}
	
	
	public function strpos_preg($haystack, $needle_exp)
	{
		preg_match($needle_exp, $haystack, $matches, PREG_OFFSET_CAPTURE);
		if (count($matches)==0)
			return -1;
		return $matches[0][1];
	}
	
	public function substring($string, $start, $end)
	{
		return substr($string, $start, $end-$start);
	}
	
	/**
	 *  Takes a string s.  Based on the tag of type tagType (e.g. "div") at tagOpenIndex, returns an object with the starting 
	 *  and ending indexes for the corresponding closing tag, in format {closeStart:int, closeEnd:int}.  If no close found, returns -1 for both values.
	 *  
	 *  Marked as public, but really only for private use!
	 **/
	public function findCorrespondingEndingTag($s, $tagOpenIndex, $tagType)
	{
		$errorReturn = array("closeStart"=>-1, "closeEnd"=>-1);
		
		$openSearch = '<' . $tagType . '';
		$closeSearch = '</' . $tagType . '>';
		$openRE = '|' . $openSearch . '|i';
		$closeRE = '|' . $closeSearch . '|i';
		
		$before = substr($s, 0, $tagOpenIndex);
		$main = substr($s, $tagOpenIndex);
		
		$nextOpen = 1 + $this->strpos_preg(substr($main,1), $openRE); // make sure we're searching for the NEXT item!
		$nextClose = $this->strpos_preg($main, $closeRE);
		
		if ($nextClose == -1)
		{
			// problem! we couldn't even find a closing tag!
			return $errorReturn;
		}
		
		// See if internal tag(s) exist; jump over each of the tags to find the real close.
		while ($nextOpen != 0 && $nextOpen < $nextClose)
		{
			$closingForTempTag = $this->findCorrespondingEndingTag($s, (strlen($before) + $nextOpen), $tagType);
			if ($closingForTempTag['closeStart'] == -1)
				return $errorReturn;
			
			// Resplit the before and main parts.
			$before = substr($s, 0, $closingForTempTag['closeEnd']);
			$main = substr($s, $closingForTempTag['closeEnd']);
			
			$nextOpen = 1+$this->strpos_preg(substr($main,1), $openSearch);
			$nextClose = $this->strpos_preg($main, $closeSearch);
		}
		
		// We've skipped any internal tags (if they existed); just return the nextClose information.
		return array(
				'closeStart'=>(strlen($before)+$nextClose), 
				'closeEnd'=>(strlen($before)+$nextClose+strlen($closeSearch)) 
			);
	}
	

	// ----- Utils -----
	
	// Use post request, unless debug=true is passed in get request.
	private function getParam($prop)
	{
		if (@$_GET['debug']=='true')
			return @$_GET[$prop];
		return @$_POST[$prop];
	}
	
	private function authenticateUser($key)
	{
		return ($key == $this->getAPIKey());
	}

	
	/**
	 * Gets the API key to be used for validation.  If none exists, then creates one.
	 * @return string
	 **/
	public function getAPIKey()
	{
		// First, see if it already exists in db
		$curVal = get_option(EasyRotatorPluginConfig::$auth_key_optionName, false);
		if ($curVal === false)
		{
			// Option hasn't been defined yet.  Create the new key and store it.
			$newKey = $this->generateNewAPIKey();
			add_option(EasyRotatorPluginConfig::$auth_key_optionName, $newKey, '', false); // don't cache, since this is only needed occasionally -- definitely not on every page load.
			return $newKey;
		}
		else
		{
			// Return existing value
			return $curVal;
		}
	}
	
	/**
	 * Resets the API key, making up a new one.
	 * @return string  Returns the new key
	 */
	public function resetAPIKey()
	{
		// Delete the current value (if one exists), then create a new one via getAPIKey().
		delete_option(EasyRotatorPluginConfig::$auth_key_optionName);
		return $this->getAPIKey();
	}
	
	private function generateNewAPIKey()
	{
		$count = 45;
		$upperCase = false;
		
		@srand((double)microtime()*1000000);
		$letters = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'); // skip l and o to avoid confusion
		$ret = '';
		$letterCount = count($letters);
		for ($i=0; $i<$count; $i++)
			$ret .= $letters[rand(1, $letterCount-1)];
		
		return ($upperCase ? strtoupper($ret) : $ret);
	}
	
	
	// ----- Content Directory Management -----
	
	private $_fakeContentDirPath = '/fakedir_er/easyrotator/'; // used for situations where we need a filler value until the user corrects errors.
	
	/**
	 * Called after activation of the plugin; used to double-check that everything is really ready to go.
	 * Attempts to create the content dir; if this fails, returns false so an error can be displayed.
	 * @return  array('success'=>boolean, 'message'=>error message if applicable)
	 */
	public function createContentDirIfNeeded()
	{
		// Determine where the content dir should be
		$contentDir = $this->getContentDir();
	
		// If we got the fake value, then the upload dir doesn't exist or isn't writable.
		if ($contentDir != $this->_fakeContentDirPath)
		{
			// The upload dir exists and is writeable.  See if we needto create the special dirs.
			$contentDir = substr($contentDir, 0, -1); // comes from getContentDir() with trailing slash.
			
			if (file_exists($contentDir) && is_dir($contentDir))
			{
				// Yeah!  Already exists.  Check writable.  <strike>Don't worry about writeable for now; that will be handled on individual calls (and shouldn't be an issue, anyway).</strike>
				if (!is_writable($contentDir) && !@chmod($contentDir, 0777))
				{
					return array('success'=>false, 'message'=>'The EasyRotator storage folder (at <code>' . $contentDir . '</code>) exists but isn\'t writable, which means that rotators can\'t be created and edited.  Please make sure that the folder has full write permissions (777).');
				}
				else
				{
					// Already writeable... all is good.
					return array('success'=>true);
				}
			}
			else
			{
				// [Need to create special dirs... try to do so.]
				// We first need to clean off the contentDirName, so we only have contentDirWrapperName.
				$firstDir = dirname($contentDir);
				$secondDir = $contentDir;
				$uploadDir = dirname($firstDir);
				
				if (!file_exists($uploadDir) || !is_dir($uploadDir))
				{
					// don't try to create upload dir, since wp does this automatically; really, we should never reach this if() branch... it's just for safety
					return array('success'=>false, 'message'=>'The uploads/ directory (at <code>' . $uploadDir . '</code>) doesn\'t exist and couldn\'t be created.  You need to manually create this directory and make it writable (777 permissions).'); // don't even bother with trying to create it, because wp_upload_dir() already tried it.
				}
				if (!file_exists($firstDir) || !is_dir($firstDir))
				{
					if (!@mkdir($firstDir)) // skip is_writable on $firstDir's parent; just try it anyway and look for failure.
						return array('success'=>false, 'message'=>'The uploads/ directory (at <code>' . $uploadDir . '</code>) exists, but doesn\'t seem to be writable.  Creation of the dedicated EasyRotator storage directory has failed.  Make sure the uploads/ directory is fully writable (777 permissions).');
					if (!is_writable($firstDir))
						return array('success'=>false, 'message'=>'The dedicated EasyRotator content directory (at <code>' . $firstDir . '</code>) exists, but doesn\'t seem to be writable.  Creation of the user content directory has failed.  Make sure the referenced directory is fully writable (777 permissions).');
				}
				if (!file_exists($secondDir) || !is_dir($secondDir))
				{
					if (!@mkdir($secondDir))
						return array('success'=>false, 'message'=>'The dedicated EasyRotator content directory (at <code>' . $firstDir . '</code>) exists, but doesn\'t seem to be writable.  Creation of the user content directory has failed.  Make sure the referenced directory is fully writable (777 permissions).');
					if (!is_writable($secondDir))
						return array('success'=>false, 'message'=>'The dedicated EasyRotator user content directory (at <code>' . $secondDir . '</code>) exists, but doesn\'t seem to be writable.  No rotators will be able to be created.  Make sure the referenced directory is fully writable (777 permissions).'); // we created it, but it's not writable which defeats the purpose.
				}
				
				// If we've made it to here, we've successfully created the dir.
				return array('success'=>true);
			}
		}
		else
		{
			// The upload dir wasn't able to be retrieved from wp.  Thus, it doesn't exist or isn't writable.  Need to warn user.  return false!
			return array('success'=>false, 'message'=>'The WordPress uploads directory (usually wp-content/uploads) doesn\'t exist or isn\'t writable.  Please make sure this directory exists and is writable.');
		}
	}
	
	private function getContentDir()
	{
		// Get the upload dir info
		$uploadInfo = wp_upload_dir();
		if ($uploadInfo['error'] === false)
		{
			// The upload dir exists; no errors.  Return the path (without creating it; that was done upon activation via above createContentDirIfNeeded() method).
			$uploadDir = $uploadInfo['basedir'];
			return ($uploadDir . '/' . EasyRotatorPluginConfig::$contentDirWrapperName . '/' . EasyRotatorPluginConfig::$contentDirName . '/');
		}
		
		// If we made it to here, the upload dir doesn't exist or isn't writable.  Need to warn user.  Return fake path that will trigger other errors.
		return $this->_fakeContentDirPath;
	}
	
	private function getContentDirURL()
	{
		// Get the upload dir info
		$uploadInfo = wp_upload_dir();
		if ($uploadInfo['error'] === false)
		{
			// The upload dir exists; no errors.  Return the path (without creating it; that was done upon activation via above createContentDirIfNeeded() method).
			$uploadURL = $uploadInfo['baseurl'];
			return ($uploadURL . '/' . EasyRotatorPluginConfig::$contentDirWrapperName . '/' . EasyRotatorPluginConfig::$contentDirName . '/');
		}
		else
		{
			// The upload dir doesn't exist or isn't writable.  Need to warn user.  Return fake path that will cause failures.
			return $this->_fakeContentDirPath;
		}
	}
	
	// --- WordPress asset utils ---
	
	/**
	 * Retrieves info about photos in the media library
	 * @param array $params  - details below
	 * @return array('photos' => array( array('thumb_id', 'src', 'thumb', 'title', 'description') , ...) Array of all photos
	 				 'photos_found' => total number of results for this search
	 				 )
	 */
	public static function getLibraryPhotoInfo($params)
	{
		/*
			$params format:
			
			limit: (int) max number of photos to return
			offset: (int) where to start the return
	        postParent: (int) optional, the ID of the post_parent for which we want to look
			search: (str) filter by search string; '' -> no filter
			year: (int) filter by year; 0 -> no filter
			month: (int) filter by month; 0 -> no filter
		*/
		
		$props = is_array($params) ? $params : array();
		
		// Build WP_Query args, applying params
		$args = array(
			'post_type'			=> 'attachment',
			'post_mime_type'	=> 'image', // only images!
			'orderby'			=> 'date', //'menu_order',
			'order'				=> 'DESC', //'ASC',
			'post_status'		=> 'any',
		);
		
		$limit = intval( @$props['limit'] );
		if ($limit > 0)
			$args['posts_per_page'] = $limit;
			
		$offset = intval( @$props['offset'] );
		if ($offset > 0)
			$args['offset'] = $offset;

        if (isset($props['postParent']) && $props['postParent'] != -1)
        {
            $postParent = intval( $props['postParent'] );
            $args['post_parent'] = $postParent;
        }
        else
        {
            $search = @$props['search'];
            if ($search != '')
                $args['s'] = $search;

            $year = intval( @$props['year'] );
            if ($year > 0)
                $args['year'] = $year;

            $month = intval( @$props['month'] );
            if ($month > 0)
                $args['month'] = $month;
        }

		// Execute the query
		$query = new WP_Query();
		$query->query( $args );
		
		$photos_found = $query->found_posts;
		
		// Loop through images, build the output
		$photos = array();
		
		foreach ( $query->posts as $post )
		{
			$thumb_id = $post->ID;
			$srcObj = wp_get_attachment_image_src( $thumb_id, 'full' );
			$thumbObj = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
			$src = $srcObj[0];
			$thumb = $thumbObj[0];
			
			$title = $post->post_title;
			if (!$title)
				$title = '';
				
			$description = $post->post_content; //$post->post_excerpt; // post_excerpt = caption, post_content = description
			if (!$description)
				$description = '';
			
			// maybe TODO: set link to permalink for post_parent [if exists]?
			
			$photos[] = array(
				'thumb_id' => $thumb_id,
				'src' => $src,
				'thumb' => $thumb,
				'title' => $title,
				'description' => $description,
			);
		}
		
		return array(
			'photos_found'	=> $photos_found,
			'photos'		=> $photos,
		);
	}
	
	/**
	 * Retrieves featured photos for a number of posts; specific details determined by $params
	 * @param array $params  - details below
	 * @return array( array('post_id', 'thumb_id', 'src', 'thumb', 'title', 'description', 'link') , ...)  Array of all photos
	 */
	public static function getFeaturedPhotoInfo($params)
	{
		/* 
			$params format:
		
			data-filter: recent | tags | cats
				data-filterDetail (only applies if tags or cats for above prop): all:12,24,26  or any:12,24,26    using tag IDs or cat IDs
			data-limit: (int) max number of photos to return; 0 = unlimited
			data-order: rand | desc [default] | asc
			data-excludeCurrent: true | false [default]
		    data-fullSize: 'full' [default] | 'large' | 'medium' | 'thumbnail' | '320,240'  (in pixels)
		    data-thumbSize: 'full' | 'large' | 'medium' | 'thumbnail' [default] | '320,240'  (in pixels)
		*/
		
		$props = is_array($params) ? $params : array();
		
		// Build the args for get_posts based on our params/props
		$args = array(
			'post_type'		=> 'post',
			'post_status'	=> 'publish',
			'order'			=> 'DESC',
		);
		
		$filterType = @$props['data-filter'];
		$filterDetail = @$props['data-filterDetail'];
		if ($filterType == 'tags' || $filterType == 'cats')
		{
			// Parse filter detail (e.g. any:1,5,6 or all:2,8,9)

			// - See if method is 'any' or 'all'
			$method = 'any';
			if (preg_match('|^all:|i', $filterDetail))
				$method = 'all';

			// - Get IDs
			$idList = preg_replace('|^.*?:|i', '', $filterDetail);
			$ids = explode(',', $idList);
			$ids = array_map('intval', $ids);

			// Set the filter			
			if ($filterType == 'tags')
				$arg = ($method == 'any' ? 'tag__in' : 'tag__and');
			else
				$arg = ($method == 'any' ? 'category__in' : 'category__and');

			$args[ $arg ] = $ids;
		}
		
		if (intval(@$props['data-limit']) > 0)
			$args['numberposts'] = intval($props['data-limit']);
			
		$r = @$props['data-order'];
		if ($r == 'asc')
			$args['order'] = 'ASC';
		elseif ($r == 'rand')
			$args['orderby'] = 'rand';
        else //default here for good measure
            $args['order'] = 'DESC';

		if (@$props['data-excludeCurrent'] == 'true' && is_single()) // this check may be revised based on user feedback.
		{
			global $wp_query;
			$cur_post_id = -1;
			try
			{
				$cur_post_id = $wp_query->post->ID;
			}
			catch (Exception $e) {}
			
			if ($cur_post_id != -1 && $cur_post_id != null)
			{
				$args['post__not_in'] = array($cur_post_id);
			}
		}
		
		
		// Args built; get the posts.
		$posts = get_posts($args);

        // Determine if custom image sizes have been specified
        $fullSize = 'full';
        if (isset($props['data-fullSize']))
        {
            $fullSizeTemp = $props['data-fullSize'];
            $isDims = preg_match('|^(\\d+),(\\d+)$|', $fullSizeTemp, $matches);
            if (in_array($fullSizeTemp, array('thumbnail', 'medium', 'large', 'full')))
                $fullSize = $fullSizeTemp;
            elseif ($isDims)
                $fullSize = array(intval($matches[1]), intval($matches[2]));
        }
        $thumbSize = 'thumbnail';
        if (isset($props['data-thumbSize']))
        {
            $thumbSizeTemp = $props['data-thumbSize'];
            $isDims = preg_match('|^(\\d+),(\\d+)$|', $thumbSizeTemp, $matches);
            if (in_array($thumbSizeTemp, array('thumbnail', 'medium', 'large', 'full')))
                $thumbSize = $thumbSizeTemp;
            elseif ($isDims)
                $thumbSize = array(intval($matches[1]), intval($matches[2]));
        }

		// Build output
		$photos = array();
		
		foreach ($posts as $post)
		{
			$post_id = $post->ID;
			if (has_post_thumbnail( $post_id ))
			{
				$thumb_id = get_post_thumbnail_id( $post_id );
				$srcObj = wp_get_attachment_image_src( $thumb_id, $fullSize );
				$thumbObj = wp_get_attachment_image_src( $thumb_id, $thumbSize );
				$src = $srcObj[0];
				$thumb = $thumbObj[0];
				
				$title = $post->post_title;
				if (!$title)
					$title = '';
					
				$description = $post->post_excerpt;
				if (!$description)
					$description = '';
					
				$link = get_permalink( $post_id );
				
				$photos[] = array(
					'post_id' => $post_id,
					'thumb_id' => $thumb_id,
					'src' => $src,
					'thumb' => $thumb,
					'title' => $title,
					'description' => $description,
					'link' => $link,
				);
				
			} // if hasthumb
		} // foreach post
		
		return $photos;
	}
	
	/**
	 * A method that sits on top of getFeaturedPhotoInfo; passed the same param obj, returns the HTML to be used within
	 * a rendered rotator.  USES ADDITIONAL PROPERTIES IN THE $params PARAM, THOUGH!  See below for details.
	 */
	public static function getFeaturedPhotoDynamicHTML($params)
	{
		/**
		  Additional supported properties:
			data-includeExcerpt: true [default] | false
			data-includeLinks: true [default] | false
			data-linkTarget: _self
		*/
		
		// Parse params
		$includeExcerpt = (@$params['data-includeExcerpt'] != 'false');
		$includeLinks = (@$params['data-includeLinks'] != 'false');
		$linkTarget = '_self';
		if (isset($params['data-linkTarget']))
			$linkTarget = $params['data-linkTarget'];
	
	
		// Get the requested photos
		$photos = self::getFeaturedPhotoInfo( $params );
		
		// Transform into HTML
		$codeOut = '';
		
		foreach ($photos as $photo)
		{
			// Get variables in this scope.
			extract($photo);
		
			$codeOut .= "\n<li>";
			
			$codeOut .= "\n	";
			if ($includeLinks)
			{
				$codeOut .= '<a class="mainLink" href="' . htmlspecialchars($link) . '"';
				if ($linkTarget != '_self')
					$codeOut .= ' target="' . htmlspecialchars($linkTarget) . '"';
				$codeOut .= '>';
			}
			$codeOut .= '<img class="main" src="' . htmlspecialchars($src) . '"';
			if ($title != '')
				$codeOut .= ' alt="' . htmlspecialchars($title) . '"';
			$codeOut .= ' />';
			if ($includeLinks)
				$codeOut .= '</a>';
			
			$codeOut .= "\n	" . '<img class="thumb" src="' . htmlspecialchars($thumb) . '" />';
			
			if ($title != '')
				$codeOut .= "\n	" . '<span class="title">' . $title . '</span>';
			if ($description != '' && $includeExcerpt)
				$codeOut .= "\n	" . '<span class="desc">' . $description . '</span>';

			$codeOut .= "\n</li>";
		}
		
		return $codeOut;
	}
	
}
endif; // end class_exists check


if ( !class_exists('EasyRotatorWPUtils') ):
class EasyRotatorWPUtils
{
	/**
	 * Attempts to determine the maximum upload size.  This is used for a warning in the wizard 
	 * when trying to upload jumbo files.
	 * @return int  Returns the maximum number of upload bytes.  If it's 0, then it probably failed due to ini_get failing.
	 */
	public static function getMaxUploadSize()
	{
		$upload_max = self::convertFriendlyToBytes(@ini_get('upload_max_filesize'));
		$post_max = self::convertFriendlyToBytes(@ini_get('post_max_size'));
		$memory_limit = self::convertFriendlyToBytes(@ini_get('memory_limit'));
		return min($upload_max, $post_max, $memory_limit);
	}
	
	/**
	 * Converts byte formats from php.ini -- e.g. 32M -- to raw bytes.
	 * @param string  $size
	 * @return int  Returns raw byte equivalent
	 */
	public static function convertFriendlyToBytes($size) 
	{
		$size = trim($size);
		$bytes = (int) $size;
		$suffix = strtolower(substr($size, -1));
		if ($suffix == 'k')
			$bytes *=  1024;
		elseif ($suffix == 'm')
			$bytes *= 1024 * 1024;
		elseif ($suffix == 'g')
			$bytes *= 1024 * 1024 * 1024;
		return $bytes;
	}
	 
	/**
	 * Passed an erc_blablabla directory, removes any existing content_NNN.zip files and loads 
	 * the contents of the content/ folder into a new zip. 
	 * @param string $path  The path to the erc_blablabla rotator directory
	 * @return string  Returns the name of the new ZIP file that's available for download.
	 */
	public static function prepareContentsZIP($path)
	{
		// Load the zip libraries
		require_once(dirname(__FILE__) . '/includes/dZip.inc.php');
		require_once(dirname(__FILE__) . '/includes/dUnzip2.inc.php');
	
		// Remove any existing content_NNNN.zip files.
		$handle = opendir($path);
		while (false !== ($item = readdir($handle)))
		{
			if (preg_match('|^content_\d+.zip$|', $item))
			{
				unlink($path . '/' . $item);
			}
		}
		closedir($handle);
		
		// Zip up the contents/ into a new content_nnnnnnn.zip file.
		$zipName = 'content_' . time() . '.zip';
		$zipPath = $path . '/' . $zipName;
		$newzip = new dZip($zipPath);

		self::copyFilesIntoZip($path . '/content', $newzip);

		// Save the new file
		$newzip->save();
		
		return $zipName;
	}
	
	private static function copyFilesIntoZip($directory, $zip, $pathPrefix = '')
	{
		$handle = opendir($directory);
		while (false !== ($item = readdir($handle)))
		{
			if ($item != '.' && $item != '..' && !preg_match('|^\.|', $item)) // just omit all .whatever files.  The rotator shouldn't be playing with them.
			{
				$path = $directory . '/' . $item; 
				if (is_dir($path)) 
				{
					// Create this subdir, then stuff it
					$zip->addDir($pathPrefix . $item);
					self::copyFilesIntoZip($path, $zip, $pathPrefix . $item . '/');
				}
				else
				{
					$zip->addFile($path, $pathPrefix . $item);
				}
			}
		}
		@closedir($directory);
	}
	
	
	/**
	 * Attempts to get the uploaded Filedata param's zip file and unzip it to the requeste destination
	 * @param string $dest  Path to the destination "content" dir, to which the files should be extracted.
	 * @return array('success'=>bool, 'message'=>message)  Indicates success or failure, message if failure
	 */
	public static function processUploadedZIP($dest)
	{
		// Load the zip libraries
		require_once(dirname(__FILE__) . '/includes/dZip.inc.php');
		require_once(dirname(__FILE__) . '/includes/dUnzip2.inc.php');
	
		// --- Setup stuff ---
		
		// Set Max file size
		$max_size = 222222 * 1024 * 1024; // we'll use 2 MB  --no: no max!
		
		//  Valid file extensions (images, word, excel, powerpoint, etc, etc)
		$reFileTypes = "/^\.(zip){1}$/i";
		
		// Specify PHP upload errors
		$upload_errors_raw = array (
			1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
			2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
			3 => 'The uploaded file was only partially uploaded.',
			4 => 'No file was uploaded.',
			6 => 'Missing a temporary folder.',
			7 => 'Failed to write file to disk.',
			8 => 'File upload stopped by extension.'
		);
		$upload_errors_friendly = array (
			1 => 'The uploaded file is too large.  Use some other file hosting service if you need to display large files in your rotator.',
			2 => 'The uploaded file is too large.  Use some other file hosting service if you need to display large files in your rotator.',
			3 => 'The uploaded file was only partially uploaded.  Please try again.',
			4 => 'No file was uploaded.',
			6 => 'Server error occurred (missing temporary folder).',
			7 => 'Server error occurred (failed to write file to disk).',
			8 => 'Internal error: File unable to be uploaded (file upload stopped by extension).  File may be invalid.',
			9 => 'Internal error: The file type you uploaded is not allowed.  Package all files in a single ZIP file.'
		);
		
		// FINALLY ... Process the uploaded files
		if (empty($_FILES))
		{
			return array('success'=>false, 'message'=>'No file was uploaded.');
		}
		
		// Check for upload errors
		if ($_FILES['Filedata']['error'] && $_FILES['Filedata']['error'] != '4')
		{
			return array('success'=>false, 'message'=>$upload_errors_friendly[$_FILES['Filedata']['error']]);
		}
		
		
		//If there is a file, process it.
		if (is_uploaded_file($_FILES['Filedata']['tmp_name']))
		{
			$file_name = $_FILES['Filedata']['name'];
			$file_type = $_FILES['Filedata']['type'];

			//  sanitize file name
		    //     - remove extra spaces/convert to _,
		    //     - remove non 0-9a-Z._- characters,
		    //     - remove leading/trailing spaces
		    //  check if under max size,
		    //  check file extension for legal file types
		    $safe_filename = preg_replace(
		                     array("/\s+/", "/[^-\.\w]+/"),
		                     array("_", ""),
		                     trim($_FILES['Filedata']['name']));
			
			$file_size = $_FILES['Filedata']['size'];
			if ($file_size > $max_size) 
			{
				return array('success'=>false, 'message'=>'The uploaded file is too large.  Use some other file hosting service if you need to display large files in your rotator.');
			}
			if (preg_match($reFileTypes, strrchr($safe_filename, '.')) !== 1)
			{
				return array('success'=>false, 'message'=>$upload_errors_friendly[9]); // file type not allowed
			}
			
			// Create the directory and file path we're uploading to...
			/*$uniqueDirName = time() . '_rand' . getUniqueCode(8);
			$newDir = $targetDir . '/' . $uniqueDirName;
			$newFile = $newDir . '/' . $safe_filename;
			if (!mkdir($newDir, 0755))
			{
				exitWithError('Unable to create upload location on server.');
			}
			
			if (move_uploaded_file($_FILES['Filedata']['tmp_name'], $newFile)) 
			{
				if (!chmod($newFile, 0744)) // set permissions for readable, not executable
				{
					exitWithError('Server error occurred (unable to make uploaded file accessible).  Please try again or contact support.');
				}
				// --------------------
				// SUCCESS!!!
				// --------------------
				$out = array(
					'success' => 'true', 
					'message' => 'File successfully uploaded.', 
					'filename' => $safe_filename, 
					'file_id' => $uniqueDirName . '/' . $safe_filename
				);
				echo(json_encode($out));
				//sleep(10);
				exit();
				// --------------------
				// --------------------
				
			}
			else
			{
				return array('success'=>false, 'message'=>'Server error occurred (upload failed).  Please try again.');
			}*/

            // Move the uploaded file; we were passed a /content path, so we'll temporarily place the zip in the parent dir
            $tempZipLocation = dirname($dest) . '/temp.zip';
            if (file_exists($tempZipLocation))
            {
                if (!@unlink($tempZipLocation))
                {
                    return array('success'=>false, 'message'=>'Unable to remove temporary ZIP file.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory.');
                }
            }
            if (!@move_uploaded_file($_FILES['Filedata']['tmp_name'], $tempZipLocation))
            {
                return array('success'=>false, 'message'=>'Unable to move uploaded file.  Please double-check that 777 write permissions are fully enabled on the wp-content/uploads/EasyRotatorStorage/ directory and that the server is properly configured to handle file uploads.');
            }

			// Attempt to unzip file file
			$zip = new dUnzip2($tempZipLocation);

			// Activate debug
			//$zip->debug = true;
			
			// Unzip all the contents of the zipped file
			$zip->getList();
			$zip->unzipAll($dest);

            // Remove the temporary ZIP file
            @unlink($tempZipLocation);
			
			return array('success'=>true);
			
		}
		else
		{
			return array('success'=>false, 'message'=>'Server error occurred (invalid file).  File may be invalid.');
		}
	}
	
}
endif; // end class_exists check


$erwp = new EasyRotatorWP();


?>
<?php

/*
 *
 *
 * This Class implements the methods defined in the STANDARD_IMAGE content model
 */

class FileManipulation {
	function FileManipulation() {

	}

	function manipulateZip($file) {

		$filename = substr(strrchr($file, '/'), 1);
		
		$output_path = $_SERVER['DOCUMENT_ROOT'] . base_path() . file_directory_path() . '/' . basename($filename, ".zip");
		$output_file = $_SERVER['DOCUMENT_ROOT'] . base_path() . file_directory_path() . '/' . $filename;
		$exec = exec("mkdir $output_path");

		$exec = exec("unzip -q $output_file -d $output_path");
		$list = array ();
		
		$exec = exec("ls -l  $output_path/*", $list);
		return $list;

	}
	function ingestAudio($file) {
		$filename = substr(strrchr($file, '/'), 1);
		$output_path = $_SERVER['DOCUMENT_ROOT'] . base_path() . file_directory_path() . '/' . $filename;
		$_SESSION['fedora_ingest_files']["MP3"] = $output_path;
	}

	function getAudioMetadata(& $form_values) {
	
		module_load_include('php', 'Fedora_Repository', 'mimetype');
		global $base_url;
		
		$fileUrl = $form_values["ingest-file-location"];
		$file_size = filesize($fileUrl);
		$file_md5 = md5_file($fileUrl);
		$file_path = $base_url . '/fedora/repository/' . $form_values['pid'] . '/FULL_SIZE';
		$exec = exec('file '.str_replace(' ','\ ',$fileUrl));
	
		$line = explode(':', $exec);
		$data = explode(',', $line[sizeof($line)-1]);
		
		$form_values['aud_compression'] = $data[0] . ' ' . $data[1];
		$form_values['aud_bitrate'] = $data[3];
		$form_values['aud_samplingfrequency'] = $data[4];
		$form_values['aud_channel'] = $data[5];
		$form_values['aud_mime'] = mime_content_type($fileUrl);

		$form_values['aud_size'] = $file_size;
		$form_values['aud_md5'] = $file_md5;
		$form_values['aud_file'] = $file_path;
		
	}

	function getVideoMetadata(& $form_values) {
		
		module_load_include('php', 'Fedora_Repository', 'mimetype');
		global $base_url;
		
		$fileUrl = $form_values["ingest-file-location"];
		$file_size = filesize($fileUrl);
		$file_md5 = md5_file($fileUrl);
		$file_path = $base_url . '/fedora/repository/' . $form_values['pid'] . '/FULL_SIZE';
	    $ffmpegInstance = new ffmpeg_movie($fileUrl);
	
		$form_values['vid_framerate'] =  $ffmpegInstance->getFrameRate();
		$form_values['vid_frameheight'] =  $ffmpegInstance->getFrameHeight();
		$form_values['vid_framewidth'] =  $ffmpegInstance->getFrameWidth();
		$form_values['vid_bitrate'] =  $ffmpegInstance->getBitRate();
		$form_values['vid_mime'] = mime_content_type($fileUrl);
		$form_values['vid_size'] = $file_size;
		$form_values['vid_md5'] = $file_md5;
		$form_values['vid_file'] = $file_path;
		
	}
}
?>
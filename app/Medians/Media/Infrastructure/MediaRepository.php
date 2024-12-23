<?php

namespace Medians\Media\Infrastructure;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Medians\Uploads\Domain\MediaUpload;

class MediaRepository 
{

	public $dir;

	public $_dir;

	public $images_dir = '/uploads/images/';

	public $files_dir = '/uploads/files/';

	public $audio_dir = '/uploads/audio/';

	public $customers_dir = '/uploads/customers/';

	public $videos_dir = '/uploads/videos/';


	public function getList($type = 'media', $user = null)
	{

		$this->setDir($type);

		return $this->fetchMedia($type, $user);
	}


	public function setDir($type)
	{

		switch ($type) 
		{
			case 'files':
				$this->_dir = $this->files_dir;
				break;

			case 'audio':
				$this->_dir = $this->audio_dir;
				break;
							
			case 'video':
				$this->_dir = $this->videos_dir;
				break;
							
			default:
				$this->_dir = $this->images_dir;
				break;
		}

		if (is_dir($_SERVER['DOCUMENT_ROOT'].$this->_dir))
		{
			$this->dir = $_SERVER['DOCUMENT_ROOT'].$this->_dir;
		}

		return $this;
	}


	public function fetchFolder($type)
	{
		$data = [];
		foreach (glob($this->dir.'*.*') as $key => $value) 
		{
			$ext = explode('.', $value);
			if (in_array(end($ext),  $this->getTypes($type)))
			{
				$data[] = $this->setMedia($value, ($key+1)); 
			}
		}

		return $data;
	}


	public function fetchMedia($type, $user)
	{
		$data = [];
		$items = MediaUpload::orderBy('created_at', 'DESC')->get();
		foreach ($items as $key => $value) 
		{
			$ext = explode('.', $value->path);
			if (in_array(end($ext),  $this->getTypes($type)))
			{
				$data[] = $this->setMedia($value->path, ($key+1)); 
			}
		}

		return $data;
	}


	public static function setMedia($value, $id = 1)
	{
		$filepath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $value);
		return [
			'id' => $id, 
			'file_name' => $filepath, 
			'download_url' => $filepath, 
			'image'=>[
				'width' => '', 
				'height' => ''
			],
			'data_url'=> $filepath
		];
	}


	public static function getTypes($type)
	{
		$app = new \config\APP;

		$settings = $app->SystemSetting();

		switch ($type) 
		{
			case 'files':
				return ['html', 'pdf', 'doc', 'docx', 'xls', 'xlsx']; 
				break;

			case 'audio':
				return explode(',', $settings['audio_allowed_ext'] ?? 'mp3,wav,ogg')  ; 
				return ['mp3', 'wav', 'oog']; 
				break;
			
			case 'video':
				return explode(',', $settings['video_allowed_ext'] ?? 'mp4,webm'); 
				break;
			
			default:
				return explode(',', $settings['pictures_allowed_ext'] ?? 'png,jpg,webp,jpeg,bmp,svg')  ; 
				break;
		}
	}


	public function upload(UploadedFile $file, $type = 'media', $customName = null)
    {

		try {
			
			$this->setDir($type);

			$ext = $file->guessExtension();

			$mimeType = $file->getMimeType();
			if ($ext === 'bin' && $mimeType === 'application/octet-stream' ) {
				$ext = 'mp3'; // Force extension to 'mp3'
			}

			$this->validate($type, $ext);
			
			$originalFilename = $customName ? rand(9999,999999) : pathinfo( $file->getClientOriginalName(), PATHINFO_FILENAME);
			$safeFilename = $this->slug($originalFilename);
			$fileNewName = $safeFilename.'-'.uniqid().'.'.$ext;
			$store = MediaUpload::addItem($this->_dir.$fileNewName, $type);

            $move = $file->move($this->dir, $fileNewName);

			return $fileNewName;

        } catch (FileException $e) {
			throw new \Exception("Error uploading  3 " . $e->getMessage(), 1);
        }

    }

	public function validate($type, $ext)
	{
		if (!in_array($ext, $this->getTypes($type)))
		{
			throw new \Exception("This file ext $ext is not allowed $type", 1);
		}	
	}

    public function delete($file)
    {

    	$filepath = $_SERVER['DOCUMENT_ROOT'].$file;

		$delete = MediaUpload::where('path', $file)->delete();

    	if (is_file($filepath))
    	{
    		return ($delete && unlink($filepath));
    	}

		return $delete;
    }

    public function resize($file, $w=null, $h='-1')
    {

		$app = new \config\APP;

		$settings = $app->SystemSetting();

    	$filepath = $_SERVER['DOCUMENT_ROOT'].$file;
    	$output = str_replace(['/images/','/img/'], '/thumbnails/', str_replace(['.png','.jpg','.jpeg', '.webp'], $w.'.webp', $filepath));

		if (is_file($output))
		{
			return str_replace($_SERVER['DOCUMENT_ROOT'], '', $output);
		}
		
    	if (is_file($filepath))
    	{
			shell_exec($settings['ffmpeg_path'].' -i '.$filepath.' -vf scale="'.$w.':'.$h.'" '.$output);
    	}

		return str_replace($_SERVER['DOCUMENT_ROOT'], '', $output);
    }


	public function convertMediaWithFfmpeg($filepath, $output)
	{
		
		$app = new \config\APP;

		$settings = $app->SystemSetting();

		$run = shell_exec($settings['ffmpeg_path']." -i $filepath $output");

		return file_exists($output) ? $output : null;
	}

	public function cropWithFfmpeg($filepath, $output, $start = '00', $duration = 60, $settings = null)
	{
		if (file_exists($output))
			return $output;

		$ffmpeg = $settings['ffmpeg_path'];
		$run = shell_exec("$ffmpeg -ss $start -i $filepath -t $duration -c copy $output");
		return file_exists($output) ? $output : null;
	}

    public static function slug($value)
    {
    	return str_replace(['&',' ','@', '!','#','(',')','+','?'], '_', $value);
    }



}

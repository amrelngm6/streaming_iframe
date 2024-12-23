<?php

namespace Medians\Media\Application;
/*
 * 
 * YouTube API Channels PHP Class
 * 
 * Grab your YouTube channel and get all videos and playlist , search on channel and get popular videos
 * 
 * PHP Version >= 5.6
 *
 * Author Tatwerat-Team 
 * 
 * Author-Account http://codecanyon.net/user/tatwerat-team 
 * 
 * Version 1.3.6
 *
 */

class YoutubeService {

	public $Key;
	public $Error;

	public function __construct($appKey) {
		$this->Key = $appKey;
	}

	// Setup API key from your google app
	public function API($query) {
		if ($query) {
			return ('https://www.googleapis.com/youtube/v3/' . $query);
		}
		else {
			$this->Error = 'Must be enter your api query';
			$this->error();
		}
	}

	// API curl request data
	function getAPI($url, $post_parameters = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if (!empty($post)) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	// Get channel info by ID
	public function channel_info($channel_id) {
		$result = array();
		$url = $this->API("channels?part=brandingSettings,statistics,snippet&id=" . $this->safe($channel_id) . "&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				$result['name'] = $data->items[0]->snippet->title;
				$result['custom_url'] = 'https://www.youtube.com/channel/' . $data->items[0]->id;
				$result['description'] = $data->items[0]->snippet->description;
				$result['localized'] = $data->items[0]->snippet->localized;
				$result['country'] = $data->items[0]->snippet->country;
				$result['published_date'] = date('d/m/Y H:i:s', strtotime((string)$data->items[0]->snippet->publishedAt));
				$result['thumbnails']['default'] = $data->items[0]->snippet->thumbnails->default->url;
				$result['thumbnails']['medium'] = $data->items[0]->snippet->thumbnails->medium->url;
				$result['thumbnails']['high'] = $data->items[0]->snippet->thumbnails->high->url;
				$result['banners'] = $data->items[0]->brandingSettings->image;
				$result['statistics']['videos_count'] = $data->items[0]->statistics->videoCount;
				$result['statistics']['hidden_subscriber_count'] = $data->items[0]->statistics->hiddenSubscriberCount;
				$result['statistics']['views_count'] = $data->items[0]->statistics->viewCount;
				$result['statistics']['subscribers_count'] = $data->items[0]->statistics->subscriberCount;
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get channel statistics by ID
	public function channel_statistics($channel_id) {
		$result = array();
		$url = $this->API("channels?part=statistics&id=" . $this->safe($channel_id) . "&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				$result['videos_count'] = $data->items[0]->statistics->videoCount;
				$result['hidden_subscriber_count'] = $data->items[0]->statistics->hiddenSubscriberCount;
				$result['views_count'] = $data->items[0]->statistics->viewCount;
				$result['subscribers_count'] = $data->items[0]->statistics->subscriberCount;
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get channel playlists by ID
	public function channel_playlists($channel_id, $count = 10) {
		$result = array();
		$url = $this->API("playlists?part=snippet%2CcontentDetails&channelId=" . $this->safe($channel_id) . "&maxResults=$count&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				$result['playlists_count'] = $data->pageInfo->totalResults;
				for ($x = 0; $x < $count; $x++) {
					if (!empty($data->items[$x])) {
						$result['playlists'][$x]['id'] = $data->items[$x]->id;
						$result['playlists'][$x]['title'] = $data->items[$x]->snippet->title;
						$result['playlists'][$x]['published_date'] = date('d/m/Y H:i:s', strtotime((string)$data->items[$x]->snippet->publishedAt));
						$result['playlists'][$x]['video_count'] = $data->items[$x]->contentDetails->itemCount;
						$result['playlists'][$x]['url'] = 'https://www.youtube.com/playlist?list=' . $data->items[$x]->id;
						$result['playlists'][$x]['description'] = $data->items[$x]->snippet->description;
						$result['playlists'][$x]['thumbnails']['default'] = $data->items[$x]->snippet->thumbnails->default->url;
						$result['playlists'][$x]['thumbnails']['medium'] = $data->items[$x]->snippet->thumbnails->medium->url;
						$result['playlists'][$x]['thumbnails']['high'] = $data->items[$x]->snippet->thumbnails->high->url;
					}
				}
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get channel videos by ID
	public function channel_videos($channel_id, $count = 10) {
		$result = array();
		$url = $this->API("channels?id=" . $this->safe($channel_id) . "&part=snippet,contentDetails,statistics&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				foreach ($data->items as $id) {
					$ID = $id->contentDetails->relatedPlaylists->uploads;
					$result = $this->playlist_videos($ID, $count);
				}
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get playlists video by ID
	public function playlist_videos($playlist_id, $count = 10) {
		$result = array();
		$url = $this->API("playlistItems?part=snippet%2CcontentDetails&maxResults=$count&playlistId=" . $this->safe($playlist_id) . "&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				$result['videos_count'] = $data->pageInfo->totalResults;
				for ($x = 0; $x < $count; $x++) {
					if (!empty($data->items[$x])) {
						$result['videos'][$x] = $this->video_info($data->items[$x]->contentDetails->videoId);
					}
				}
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get Search on Channel By ID
	public function channel_search($channel_id, $keyword = '', $count = 10) {
		$result = array();
		$keyword = str_replace(' ', '+', $keyword);
		$url = $this->API("search?channelId=" . $this->safe($channel_id) . "&part=id&order=date&maxResults=$count&q=$keyword&type=video&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				for ($x = 0; $x < $count; $x++) {
					if (!empty($data->items[$x])) {
						$result[$x] = $this->video_info($data->items[$x]->id->videoId);
					}
				}
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get Search on YouTube Videos
	public function public_search($keyword = '', $count = 10) {
		$result = array();
		$keyword = str_replace(' ', '+', $keyword);
		$url = $this->API("search?part=id&order=date&maxResults=$count&q=$keyword&type=video&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				for ($x = 0; $x < $count; $x++) {
					if (!empty($data->items[$x])) {
						$result[$x] = $this->video_info($data->items[$x]->id->videoId);
					}
				}
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get Populars on Channel By ID
	public function channel_popular($channel_id, $count = 10) {
		$result = array();
		$url = $this->API("search?channelId=" . $this->safe($channel_id) . "&part=id&order=viewCount&maxResults=$count&type=video&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if (isset($data->items)) {
				for ($x = 0; $x < $count; $x++) {
					if (!empty($data->items[$x])) {
						$result[$x] = $this->video_info($data->items[$x]->id->videoId);
					}
				}
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get Realted Videos By Video ID
	public function related_video($id, $count = 10) {
		$result = array();
		$url = $this->API("search?part=snippet&relatedToVideoId=" . $this->safe($id) . "&type=video&maxResults=$count&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				for ($x = 0; $x < $count; $x++) {
					if (!empty($data->items[$x])) {
						$result[$x] = $this->video_info($data->items[$x]->id->videoId);
					}
				}
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get Comments By Video ID
	public function video_comments($id, $count = 10) {
		$result = array();
		$url = $this->API("commentThreads?part=snippet%2Creplies&maxResults=$count&videoId=" . $this->safe($id) . "&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				$result['comments_count'] = $data->pageInfo->totalResults;
				for ($x = 0; $x < $count; $x++) {
					if (!empty($data->items[$x])) {
						$result['comments'][$x]['id'] = $data->items[$x]->id;
						$result['comments'][$x]['text'] = $data->items[$x]->snippet->topLevelComment->snippet->textDisplay;
						$result['comments'][$x]['published_date'] = date('d/m/Y H:i:s', strtotime((string)$data->items[$x]->snippet->topLevelComment->snippet->publishedAt));
						$result['comments'][$x]['updated_date'] = date('d/m/Y H:i:s', strtotime((string)$data->items[$x]->snippet->topLevelComment->snippet->updatedAt));
						$result['comments'][$x]['author']['name'] = $data->items[$x]->snippet->topLevelComment->snippet->authorDisplayName;
						$result['comments'][$x]['author']['image'] = $data->items[$x]->snippet->topLevelComment->snippet->authorProfileImageUrl;
						$result['comments'][$x]['author']['channel_url'] = $data->items[$x]->snippet->topLevelComment->snippet->authorChannelUrl;
						$result['comments'][$x]['author']['googleplus_url'] = isset($data->items[$x]->snippet->topLevelComment->snippet->authorGoogleplusProfileUrl) ? $data->items[$x]->snippet->topLevelComment->snippet->authorGoogleplusProfileUrl : '';
						$result['comments'][$x]['like_count'] = !empty($data->items[$x]->snippet->topLevelComment->snippet->likeCount) ? $data->items[$x]->snippet->topLevelComment->snippet->likeCount : '';
						if (!empty($data->items[$x]->replies->comments)) {
							$replies = $data->items[$x]->replies->comments;
							foreach ($replies as $key => $reply) {
								$result['comments'][$x]['replies'][$key]['id'] = $reply->id;
								$result['comments'][$x]['replies'][$key]['text'] = $reply->snippet->textDisplay;
								$result['comments'][$x]['replies'][$key]['published_date'] = date('d/m/Y H:i:s', strtotime((string)$reply->snippet->publishedAt));
								$result['comments'][$x]['replies'][$key]['updated_date'] = date('d/m/Y H:i:s', strtotime((string)$reply->snippet->updatedAt));
								$result['comments'][$x]['replies'][$key]['author']['name'] = $reply->snippet->authorDisplayName;
								$result['comments'][$x]['replies'][$key]['author']['image'] = $reply->snippet->authorProfileImageUrl;
								$result['comments'][$x]['replies'][$key]['author']['channel_url'] = $reply->snippet->authorChannelUrl;
								$result['comments'][$x]['replies'][$key]['author']['googleplus_url'] = isset($reply->snippet->authorGoogleplusProfileUrl) ? $reply->snippet->authorGoogleplusProfileUrl : '';
								$result['comments'][$x]['replies'][$key]['like_count'] = !empty($reply->snippet->likeCount) ? $reply->snippet->likeCount : '';
							}

						}
					}
					return ($result);
				}
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

    public function processVideo($adaptiveFormats, $formats) {
        
        $result = array();


            foreach($adaptiveFormats as $stream) {

                $streamUrl = $stream->url;
                $type = explode(";", $stream->mimeType);

                $qualityLabel='';
                if(!empty($stream->qualityLabel)) {
                    $qualityLabel = $stream->qualityLabel;
                }

                $videoOptions[$i]['link'] = $streamUrl;
                $videoOptions[$i]['type'] = $type[0];
                $videoOptions[$i]['quality'] = $qualityLabel;
                $i++;
            }
            $j=0;
            foreach($formats as $stream) {

                $streamUrl = $stream->url;
                $type = explode(";", $stream->mimeType);

                $qualityLabel='';
                if(!empty($stream->qualityLabel)) {
                    $qualityLabel = $stream->qualityLabel;
                }

                $videoOptionsOrg[$j]['link'] = $streamUrl;
                $videoOptionsOrg[$j]['type'] = $type[0];
                $videoOptionsOrg[$j]['quality'] = $qualityLabel;
                $j++;
            }
            $result['videos'] = array(
                'adapativeFormats'=>$videoOptions,
                'formats'=>$videoOptionsOrg
            );
            
            
            return $result;
    }

    

	// Get video Data by ID
	public function video_info($id) {
		$url = $this->API("videos?id=" . $this->safe($id) . "&key=" . $this->Key . "&part=snippet,contentDetails,statistics,status");
		$result = array();
		$video_json = $this->getAPI($url);
		$data = json_decode($video_json);

		die($this->dumb_array($data));
		if ($data) {
			$result['id'] = $data->items[0]->id;
			$result['channel_id'] = $data->items[0]->snippet->channelId;
			$result['channel_title'] = $data->items[0]->snippet->channelTitle;
			$result['category_id'] = $data->items[0]->snippet->categoryId;
			$result['title'] = $data->items[0]->snippet->title;
			$result['description'] = $data->items[0]->snippet->description;
			$result['localized'] = $data->items[0]->snippet->localized;
			$result['url'] = 'https://www.youtube.com/watch?v=' . $data->items[0]->id;
			$result['published_date'] = date('d-m-Y H:i A', strtotime($data->items[0]->snippet->publishedAt));
			$result['view_count'] = $data->items[0]->statistics->viewCount;
			$result['like_count'] = !empty($data->items[0]->statistics->likeCount) ? $data->items[0]->statistics->likeCount : 0;
			$result['dislike_count'] = !empty($data->items[0]->statistics->dislikeCount) ? $data->items[0]->statistics->dislikeCount : 0;
			$result['favorite_count'] = $data->items[0]->statistics->favoriteCount;
			$result['comment_count'] = (isset($data->items[0]->statistics->commentCount)) ? $data->items[0]->statistics->commentCount : 0;
			$result['duration'] = $this->getDurationSeconds($data->items[0]->contentDetails->duration);
			$result['definition'] = $data->items[0]->contentDetails->definition;
			$result['privacy_status'] = $data->items[0]->status->privacyStatus;
			$result['tags'] = !empty($data->items[0]->snippet->tags) ? $data->items[0]->snippet->tags : '';
			$result['thumbnails']['default'] = isset($data->items[0]->snippet->thumbnails->medium->url) ? $data->items[0]->snippet->thumbnails->medium->url : '';
			$result['thumbnails']['medium'] = isset($data->items[0]->snippet->thumbnails->high->url) ? $data->items[0]->snippet->thumbnails->high->url : '';
			$result['thumbnails']['high'] = isset($data->items[0]->snippet->thumbnails->standard->url) ? $data->items[0]->snippet->thumbnails->standard->url : '';
			$result['thumbnails']['hd'] = isset($data->items[0]->snippet->thumbnails->maxres->url) ? $data->items[0]->snippet->thumbnails->maxres->url : '';
			return ($result);
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Video Player
	public function iframe_video_player($url, $width = '550', $height = '300') {
		$url = trim($url);
		$player = '';
		if ($this->checkServer(array(
			"youtube.com",
			"youtu.be"
		), $url)) {
			$player = '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $this->videoID_byUrl($url) . '" frameborder="0" allowfullscreen></iframe>';
		}
		else {
			$player = '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $url . '" frameborder="0" allowfullscreen></iframe>';
		}
		return $player;
	}

	// Get channel ID by user name
	public function channelID_byUsername($username) {
		$result = '';
		$url = $this->API("channels?part=snippet&forUsername=" . $this->safe($username) . "&key=" . $this->Key);
		$json =$this->getAPI($url);
		if ($json) {
			$data = json_decode($json);
			if ($data->items) {
				$result = $data->items[0]->id;
				return ($result);
			}
			else {
				$this->Error = 'Empty data';
				$this->error();
			}
		}
		else {
			//debug_backtrace();
			$this->Error = 'Error in get data';
			$this->error();
		}
	}

	// Get playlist ID by URL
	public function playlistID_byUrl($url) {
		$query = parse_url($url);
		parse_str($query['query'], $id);
		return (isset($id['list'])) ? $id['list'] : null;
	}

	// Get video ID by URL
	public function videoID_byUrl($url) {
		parse_str(parse_url($url, PHP_URL_QUERY), $id);
		return (isset($id['v'])) ? $id['v'] : null;
	}

	// Duration Seconds
	public function getDurationSeconds($duration) {
		preg_match_all('/(\d+)/', $duration, $parts);
		// Put in zeros if we have less than 3 numbers.
		if (count($parts[0]) == 1) {
			array_unshift($parts[0], "0", "0");
		}
		else if (count($parts[0]) == 2) {
			array_unshift($parts[0], "0");
		}
		$sec_init = $parts[0][2];
		$seconds = $sec_init % 60;
		$seconds_overflow = floor($sec_init / 60);
		$min_init = $parts[0][1] + $seconds_overflow;
		$minutes = ($min_init) % 60;
		$minutes_overflow = floor(($min_init) / 60);
		$hours = $parts[0][0] + $minutes_overflow;
		if ($hours != 0)
			return $hours . ':' . $minutes . ':' . $seconds;
		else
			return $minutes . ':' . $seconds;
	}

	// Dumb array
	public function dumb_array($array) {
		echo '<pre style="overflow:auto; width:100%;">';
		print_r($array);
		echo '</pre>';
	}

	// safe values
	private function safe($value) {
		return trim(htmlspecialchars($value));
	}

	// safe values
	private function filter_date($data) {
		return !empty($data) ? $data : null;
	}

	// check server name
	private function checkServer($domains = array(), $url = null) {
		foreach ($domains as $domain) {
			if (strpos($url, $domain) > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	// Show errors when function can't get data
	public function error() {
		if ($this->Error)
			echo('<div class="yt-error" style="padding:15px;color:red;margin:10px;border:1px solid red;border-radius:2px;">' . $this->Error . '</div>');
	}

}








/*
namespace Medians\Media\Application;
use Shared\dbaser\CustomController;

use Google\Cloud\Storage\StorageClient;

class YoutubeService extends CustomController 
{

	protected $app;

	protected $client;
    
	protected $bucketName;
	
	function __construct($apiKey)
	{
        // Create a Google Cloud Storage client
        $this->client = new \Google_Client();

        $this->client->setDeveloperKey($apiKey);

        // Bucket name
        
	}

    /**
     * Upload file to Google Storage
     * 
     * @param $filePath String ( Full path ) 
     * @param $destination String  ( Path )
     */
    /*
    function checkVideo($filePath) {

        // Create a YouTube service object
        $youtube = new \Google_Service_YouTube($this->client);

        // The video ID you want details for
        $videoId = 'e3QZ39fy2pA';

        try {
            // Call the API's videos.list method to get the video details
            $response = $youtube->videos->listVideos('snippet,contentDetails,statistics', array(
                'id' => $videoId,
            ));

            // Access the video details
            $videoDetails = $response['items'][0];
            print_r($videoDetails);
            
            // Print video details
            echo 'Title: ' . $videoDetails['snippet']['title'] . PHP_EOL;
            echo 'Description: ' . $videoDetails['snippet']['description'] . PHP_EOL;
            echo 'Published At: ' . $videoDetails['snippet']['publishedAt'] . PHP_EOL;
            echo 'Views: ' . $videoDetails['statistics']['viewCount'] . PHP_EOL;
            echo 'Likes: ' . $videoDetails['statistics']['likeCount'] . PHP_EOL;

        } catch (\Google_Service_Exception $e) {
            echo 'A service error occurred: ' . htmlspecialchars($e->getMessage());
        } catch (\Google_Exception $e) {
            echo 'An client error occurred: ' . htmlspecialchars($e->getMessage());
        }
    }
    
}
*/
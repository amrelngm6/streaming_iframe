/**
 * Main functions 
 * Audio player
 * Station player
 * 
 */
var mainAudio = jQuery('audio');
var audio, canPlay, player, audioInfo, audioObject, list, index, is_slide, filename, activeChannel, activeChannelMedia, activeStation, activeStationMedia, stationInterval, streamingStatus;
let rand = Math.random();

/**
 * Set audio volume when audio source 
 * is being changed
 */
jQuery('#player-audio').on('change', function(event) {
	audio.volume = event.target.value;
	setCookie('volume', event.target.value, 7); // Set a cookie named 'username' with value 'john_doe' that expires in 7 days
}) ;	


/**
 * Set Station audio player volume when active media 
 * is being changed
 */
jQuery(document).on('change', '#station-player-audio', function(event) {
	console.log(event)
	audio.volume = event.target.value;
	setCookie('volume', event.target.value, 7); // Set a cookie named 'username' with value 'john_doe' that expires in 7 days
}) ;	


/**
 * Start streaming Audio station
 * Update the player and the active media info
 * 
 */
jQuery(document).on('click', '.start-station', async function (i, el) {

	rand += 1
	audio ? audio.pause() : null

	jQuery('#station-player-audio').val(getCookie('volume'))
	jQuery('#station-player-pause-button').addClass('active')
	jQuery('#station-app-cover').removeClass('hidden')
	jQuery('#app-cover').addClass('hidden')
	const stationId = jQuery(this).data('station'); 
		
	await loadStation(stationId)
	
	updateStationSrc(stationId, rand)

	mainAudio.on('ended', function() {
		rand += 1
		updateStationSrc(stationId, rand)
		setTimeout(function () {
			if (audio.paused)
			{
				loadStation(stationId)
			}
		}, 5000)
	})
});


function updateStationSrc(stationId, rand)
{
	audio.src = '/stream_station?station_id='+ stationId+'&hash='+rand;
	audio.load();
	audio.play();
	audio.volume = getCookie('volume')
	loadStation(stationId)
}


/**
 * Load channel and set the Active channel object
 * 
 * @param {*} channelId ID of the channel
 * @returns {Object} activeChannel
 */
async function loadChannelJson(channelId)
{
	const response  = await $.get('/channel_json/'+channelId);

	activeChannel = JSON.parse(response)

	return activeChannel;
}



/**
 * Load Channel and set the Active channel Object
 * 
 * @param {*} channelId ID of the channel
 */
async function loadChannel(channelId)
{
	activeChannel = await loadChannelJson(channelId);
}



/**
 * Load Station and set the Active Station object
 * 
 * @param {*} stationId ID of the station 
 * @returns {Object} activeStation
 */
async function loadStationJson(stationId)
{
	const response  = await $.get('/station_json/'+stationId);

	activeStation = JSON.parse(response)

	activeStationMedia = activeStation.active_item ?? null;

	return activeStation;
}


/**
 * Load Station media and set the active Station 
 *  
 * @param {*} stationId ID of the station 
 * @param {*} play Optional to start streaming automatically
 */
async function loadStation(stationId, play = true)
{
	if (!audio) {
		audio = mainAudio[0]
	}
	
	activeStation = await loadStationJson(stationId);

	await handleStationPlayer(stationId, play)
}

/**
 * Handle the Audio Stations player
 *  
 * @param {*} stationId ID of the station 
 * @param {*} play Optional to start streaming automatically
 */
async function handleStationPlayer(stationId, play = true)
{
	rand += 1
	if (activeStationMedia && activeStation.active_item && activeStation.active_item.media_id == activeStationMedia.media_id)
	{
		streamingStatus = 'same'
		if ((audio.duration - audio.currentTime) < 1) {
			streamingStatus = 'new'
		}
	} else if (activeStation.active_item == null) {
		streamingStatus = null;
	} else {
		streamingStatus = 'new'
	}
	activeStationMedia = activeStation.active_item;

	if (streamingStatus == 'new' && play)
	{
		updateStationSrc(stationId, rand)
	}
	
	let mediaTitle = (activeStationMedia && activeStationMedia.media) ? activeStationMedia.media.name : activeStationMedia.title;
	let stationTitle = activeStation.name ?? 'UNKNOWN';
	let mediaPic = (activeStationMedia.media) ? activeStationMedia.media.picture  : activeStation.picture;
	if (activeStationMedia)
	{
		jQuery('#station-album-name').html(mediaTitle)
		jQuery('.station-stream-name').html(mediaTitle)
		jQuery('.station-streaming-picture'+activeStation.station_id).attr(mediaPic)
		jQuery('#station-track-name').html(stationTitle )
		
	} else {
		
		jQuery('#station-album-name').html('Offline')
		jQuery('.station-stream-name').html("Offline")
		jQuery('#station-track-name').html('UNKNOWN')
		if (audio && !play) {
			audio.pause()
		}
	}
	jQuery('.station-streaming-picture'+activeStation.station_id).attr( 'src', mediaPic);
	jQuery('#station-track-poster').attr( 'src', mediaPic);

}




/**
 * Start Playing Audio list of items 
 * Handle the list and play the clicked index
 */
jQuery(document).on('click', '.restart-player', function (i, el) {

	
	player = jQuery(this);
	list = JSON.parse(player.attr('data-list'))
	index = parseInt(player.attr('data-index'))
	audioInfo = player.find('.slide__audio-player');
	audioObject = list[index] ?? {};

	if (audio.src != rootURL+audioInfo.attr('data-path'))
	{
		audio.src = audioInfo.attr('data-path');
		audio.load()
		playStyles()

	} else {
		(!audio.paused ) ?  pauseStyles() : playStyles()
	}

})

jQuery(document).on('click', '.start-player', function (i, el) {
	jQuery('#station-app-cover').addClass('hidden')
	audio ? audio.pause() : null
	player = jQuery(this);
	list = JSON.parse(player.attr('data-list'))
	index = parseInt(player.attr('data-index'))
	if (player.hasClass('start-player')) {
		audioObject = list[index] ?? {};
		filename = audioObject.file ?? audioObject.filename;
		audioInfo = player.find('.slide__audio-player');
		audio = mainAudio[0];
		audio.src = '/stream_audio?audio='+ filename;
		audio.load()
		initAudioPlayer()
		playStyles()
		player.removeClass('start-player')
		player.addClass('restart-player')
		jQuery('#player-previous').removeClass('hidden')
		jQuery('#player-next').removeClass('hidden')

	} 
	if (stationInterval) {
		clearInterval(stationInterval)
	}
});



/**
 * Start Playing Single Audio item
 * For Audio page, more...
 */
jQuery(document).on('click', '.start-single-player', function (i, el) {
	player = jQuery(this);

	if (audio) {
		audio.pause()
	}

	jQuery('#station-app-cover').addClass('hidden')
	list = JSON.parse(player.attr('data-list'))
	index = player.attr('data-index')
	if (player.hasClass('start-single-player')) {
		audioObject = list[index];
		filename = audioObject.file ?? audioObject.filename;
		audioInfo = player.find('.slide__audio-player');
		audio = mainAudio[0];
		audio.src = '/stream_audio?audio='+ filename;
		audio.load()
		initAudioPlayer()
		playStyles()
		jQuery('#player-previous').addClass('hidden')
		jQuery('#player-next').addClass('hidden')
	} 
	if (stationInterval) {
		clearInterval(stationInterval)
	}

});


/**
 * Play / Pause Audio item event
 * And handle the styles
 */
jQuery('#player-pause-button').on("click", function(event) {
	if (audio.paused ) {
		playStyles();
	} else  {
		pauseStyles();
	}
});


/**
 * Go to previous item in list On click
 * Not working with ( Stations & Single audio )
 */
jQuery('#player-previous').on("click", function(event) {
	index = index ? (index - 1) : 0; 
	if (list[index])
	{
		handleFile();
	}

});


/**
 * Go to next item in list On click
 * Not working with ( Stations & Single audio )
 */
jQuery('#player-next').on("click", function(event) {
	index = list[index + 1] ? (index + 1) : index; 
	if (list[index]) {
		handleFile();
	}
});


jQuery('#volume-mute-img').on("click", function(event) {
	audio.muted = !audio.muted;
}) ;



/**
 * Play / Pause Audio stations
 */
jQuery('#station-player-pause-button').on("click", async function(event) {
	if ( audio.paused ) {
		await loadStation(activeStation.station_id)
		audio.play()
	} else  {
		audio.pause();
		if (stationInterval) {
			clearInterval(stationInterval)
		}
	}
	jQuery(this).toggleClass('active')
});




/**
 * Handle stream file info of Audio item
 * Useful for Audiobooks chapters info
 */
function handleFile()
{
	audioObject = list[index] ?? {};
	filename = audioObject.file ?? audioObject.filename;
	player = jQuery('#media-'+(audioObject.media_file_id ?? audioObject.media_id));
	audioInfo = player.find('.slide__audio-player');
	audio.src = '/stream_audio?audio='+ filename;
	audio.load()
	playStyles()
}


/**
 * Handle the circles for Audio items
 */
function handlePlayerCircles()
{

	jQuery('.audio__slider').roundSlider({
		radius: 50,
		value: 0,
		startAngle: 90,
		width: 5,
		handleSize: '+2',
		handleShape: 'round',
		sliderType: 'min-range'
	});
	jQuery(document).on('drag, change', '.audio__slider', function (e) {
		let $this = $(this);
		$this.addClass('active');
		updateAudio(e.handle.value);
	});
}


/**
 * Handle Media and player styles
 * On audio stop playing ( Pause ) 
 */
function pauseStyles() {
	audio.pause();
	player.removeClass('playing');
	player.parent().removeClass('active');
	player.addClass('paused');
	player.parent().parent().find('.wave-frame').addClass('hidden');
	document.getElementById('album-art').classList.remove('active') 
	document.getElementById('player-pause-button').classList.remove('active') 
	document.getElementById('player-track').classList.remove('active') 
}


/**
 * Handle Media and player styles
 * On audio start playing 
 */
function playStyles() {
	
	$('.js-audio').removeClass('playing');
	$('.js-audio').parent().removeClass('active');
	
	player.removeClass('paused');
	player.addClass('playing');
	player.parent().addClass('active');
	jQuery('.wave-frame').addClass('hidden');
	player.parent().parent().find('.wave-frame').removeClass('hidden');
	document.getElementById('album-art').classList.add('active') 
	document.getElementById('player-pause-button').classList.add('active') 
	
	document.getElementById('app-cover').classList.remove('hidden') 
	document.getElementById('album-name').innerHTML = audioObject.name ?? (audioObject.title ?? ''); 
	document.getElementById('track-name').innerHTML = audioObject.artist ?? ''; 
	document.getElementById('track-poster').src = '/stream?thumbnail=100&image='+ audioObject.poster ?? ''; 
	document.getElementById('track-poster').classList.add('active') 
	document.getElementById('player-track').classList.add('active') 

	setTimeout(function(){
		audio.play();
	}, 100)
}


/**
 * 
 * @param {*} e  Value to seek audio 
 */
function updateAudio(e) {

	let value = e;
	var maxduration = audio.duration;

	var y = (value / 100) * maxduration;

	audio.currentTime = y;
}


/**
 * Start initiate Audio player
 */
function initAudioPlayer() {

	jQuery('#player-audio').val(getCookie('volume'))
	audio.volume = getCookie('volume')

	let play = player.find('.play-pause'),
		circle = player.find('#seekbar-'+audioInfo.attr('data-id')),
		getCircle = circle.get(0),
		totalLength = getCircle.getTotalLength();

	mainAudio.on('loadedmetadata', function() {
		const duration = audio.duration;
	});


	
	circle.attr({
		'stroke-dasharray': totalLength,
	});

	mainAudio.on('timeupdate', (e) => {
		circle = jQuery('#seekbar-'+audioInfo.attr('data-id'))
		circle.attr({
			'stroke-dasharray': totalLength,
		});

		let currentTime = audio.currentTime,
		maxduration = audio.duration,
		calc = totalLength - (currentTime / maxduration * totalLength);

		document.getElementById('track-length').innerHTML = convertToTime(maxduration) 
		document.getElementById('current-time').innerHTML = convertToTime(currentTime) 

		circle.attr('stroke-dashoffset', calc);
		
		let value = (isNaN(maxduration) ? 0 : ((currentTime / maxduration) * 100));

		$('#'+jQuery(audioInfo).attr('data-wave-overlay')).css('right', '0')
		$('#'+jQuery(audioInfo).attr('data-wave-overlay')).css('left', 'auto')
		$('#'+jQuery(audioInfo).attr('data-wave-overlay')).css('width', (100 - value)+'%')
		$('#seek-bar').css('width', (value)+'%')

		var slider = '#circle-'+jQuery(audioInfo).attr('data-id');
		
		value ? jQuery(slider).roundSlider('setValue', value) : '';
	});

	mainAudio.on('play', (e) => {
		playStyles()
	});

	mainAudio.on('ended', () => {
		player.removeClass('playing');
        player.parent().removeClass('active');
		circle.attr('stroke-dashoffset', totalLength);
		document.getElementById('album-art').classList.remove('active') 

		jQuery('#player-next').click()

	});

	jQuery(document).on('click', '#' + jQuery(audioInfo).attr('data-wave-overlay'), function(event){
		const imageElement = document.getElementById(jQuery(audioInfo).attr('data-wave-id'));
		const imageRect = imageElement.getBoundingClientRect(); // Get image position and size
		const clickX = event.clientX - imageRect.left; // Calculate X position relative to the image
		
		var percentage = (clickX / imageElement.clientWidth) * 100;
		
		let $elem = jQuery(event.target.parentNode.parentNode).find('.js-audio');
		
		updateAudio(percentage.toFixed(2));
	
	});

	
	
	jQuery(document).on('click', '#' + jQuery(audioInfo).attr('data-wave-id'), function(event){
		const imageElement = document.getElementById(jQuery(audioInfo).attr('data-wave-id'));
		const imageRect = imageElement.getBoundingClientRect(); // Get image position and size
		const clickX = event.clientX - imageRect.left; // Calculate X position relative to the image
		
		var percentage = (clickX / imageElement.clientWidth) * 100;
		
		let $elem = jQuery(event.target.parentNode.parentNode).find('.js-audio');
		
		updateAudio(percentage.toFixed(2));
	
	});

	jQuery(document).on('click', '#s-area', function(){
		const imageElement = document.getElementById('s-area');
		const imageRect = imageElement.getBoundingClientRect(); // Get image position and size
		const clickX = event.clientX - imageRect.left; // Calculate X position relative to the image
		
		var percentage = (clickX / imageElement.clientWidth) * 100;
		audio.currentTime = (percentage / 100) * audio.duration;
	})
	
}

/**
 * Convert Date to time
 * @param {*} val  
 */
function dateToTime(d)
{
	return (d.getHours() > 9 ? d.getHours() : '0'+d.getHours() ) +':'+ (d.getMinutes() > 9 ? d.getMinutes() : '0'+d.getMinutes() ) + ':' + (d.getSeconds() > 9 ? d.getSeconds() : '0'+d.getSeconds() );
}


/*
converts String to hh:mm:ss or mm:ss
*/
function toHHMMSS(val) {
	var sec_num = parseInt(val, 10);
	var hours = Math.floor(sec_num / 3600);
	var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
	var seconds = sec_num - (hours * 3600) - (minutes * 60);

	if (hours < 10) { hours = "0" + hours; }
	if (minutes < 10) { minutes = "0" + minutes; }
	if (seconds < 10) { seconds = "0" + seconds; }

	// only mm:ss
	if (hours == "00") {
		var time = minutes + ':' + seconds;
	}
	else {
		var time = hours + ':' + minutes + ':' + seconds;
	}

	return time;
}



function convertToTime(num) {
	return toHHMMSS(num);
}



/**
 * Sticky sidebar at scroll
 * 
 * @return void 
 */
function stickyScroll()
{
	const sidebar = document.querySelector('#filter-side');
	const container = document.querySelector('#filter-parent');
	
	if (!sidebar || !container)
		return null;

	function fixSidebar() {
	const rect = sidebar.getBoundingClientRect();
	const containerRect = container.getBoundingClientRect();
	const distanceFromBottom = containerRect.bottom - rect.height + 400; // Adjust with the top value

	if (window.scrollY >= distanceFromBottom && window.innerWidth > 1000)  {
		sidebar.style.position = 'fixed';
		sidebar.style.bottom = '0';
		sidebar.style.width = '13rem';
	} else {
		sidebar.style.position = 'relative';
		sidebar.style.top = '0px'; // Adjust with the top value
		sidebar.style.width = '100%';
	}
	}

	window.addEventListener('scroll', fixSidebar);
	window.addEventListener('resize', fixSidebar);
}



/**
 * Sticky sidebar at scroll for Playlist page
 * 
 * @return void 
 */
function stickyPlaylist()
{
	const sidebar = document.querySelector('#playlist-side');
	const container = document.querySelector('#playlist-parent');
	
	if (!sidebar || !container)
		return null;

	function fixSidebar() {
	const rect = sidebar.getBoundingClientRect();
	const containerRect = container.getBoundingClientRect();
	const distanceFromBottom = containerRect.top + 80; // Adjust with the top value
	
	if (window.innerWidth > 1024)
	{

		if (window.scrollY >= distanceFromBottom) {
			sidebar.style.position = 'fixed';
			sidebar.style.top = '10px';
		} else {
			sidebar.style.position = 'relative';
		}
	}
	}

	window.addEventListener('scroll', fixSidebar);
	window.addEventListener('resize', fixSidebar);
}


jQuery(document).on('click', '.active-parent', function(item){

	jQuery(this).parent().toggleClass('active')
})


jQuery(document).on('click', '.active-child', function(item){

	jQuery(this).parent().toggleClass('active')
})


setTimeout(function() {
	reloadFuncs()
}, 1000);


function reloadFuncs() 
{
	
	const currentURL = window.location.href;
	const page = currentURL.split("/")
	const a =  ((page[page.length - 1]).replace('.html', ''))

	stickyScroll()
	handlePlayerCircles()
	stickyPlaylist();
    showSlides()
	handleSlides()

	document.querySelectorAll('[data-multi-select]').forEach(select => new MultiSelect(select));
}

jQuery(document).on('change', '#imageInput', function(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const imagePreview = document.getElementById('imagePreview');
        imagePreview.src = e.target.result;
        imagePreview.style.display = 'block';
    }
    
    if (file) {
        reader.readAsDataURL(file);
    }
});

jQuery(document).on('click','.pCard_add', function () {
	jQuery('#'+jQuery(this).attr('data-id')).toggleClass('pCard_on');
});


/**
 * When user back to previous page
 * Load page in ajax
 */
window.addEventListener('popstate', function (e) {
	e.preventDefault()
    var state = e.state;
    if (state !== null) {
		loadPage(e.target.location.pathname)
    }
});






















/**
 * Calendar Page
 * Append selected item for calendar date range
 * 
 * @param {String} elementId 
 */
function appendRangeSelectedItem(elementId, type = 'channel')
{
	jQuery(`#selected_media_list`).append(jQuery(elementId).html())
	
	let elements = jQuery('#selected_media_list').find('.range-selected-media');
	let element;
	jQuery('#'+type+'-range-selected-duration').val('0')

	for (var i = 0; i < elements.length; i++) {
		element = elements[i].dataset;
		handleSelectedDuration(element.id, element.uniqueId, element.duration, type)
	}
}


/**
 * Calendar Page
 * Remove selected date range item
 */
function removeRangeSelectedItem(elementId, type = 'channel')
{
	jQuery(elementId).remove()
	
	let elements = jQuery('#selected_media_list').find('.range-selected-media');
	let element;
	jQuery('#'+type+'-range-selected-duration').val('0')

	for (var i = 0; i < elements.length; i++) {
		element = elements[i].dataset;
		handleSelectedDuration(element.id, element.uniqueId, element.duration, type)
	}
}

/**
 * Calendar Page
 * Handle selected date range item
 */
function handleSelectedDuration(id, uniqueId, duration, type = 'channel')
{

	let dateTime = jQuery(`#${type}-range-date`).val() +' '+ jQuery(`#${type}-range-start`).val();
	var d = new Date(dateTime);
	d.setSeconds(d.getSeconds() + parseInt(jQuery(`#${type}-range-selected-duration`).val()));
	var from = dateToTime(d)

	jQuery(`#selected-start-at-${id}-${uniqueId}`).val( from )
	jQuery(`#${type}-range-selected-duration`).val( parseInt(jQuery(`#${type}-range-selected-duration`).val()) + parseInt(duration) )
	var d = new Date(dateTime);
	d.setSeconds(d.getSeconds() + parseInt(jQuery(`#${type}-range-selected-duration`).val()));
	var to = dateToTime(d)

	jQuery(`#selected-playing-duration-${id}-${uniqueId}`).html(from+' | '+to)

	jQuery(`#${type}-range-selected-duration-text`).html(toHHMMSS(jQuery(`#${type}-range-selected-duration`).val()))

	jQuery(`#video-list-${id}-${uniqueId}`).remove()
	jQuery(`#range-item-submit-button`).removeClass(`hidden`);
}








/**
 * Get IFrame code example for active link
 * 
 * @param {*} link Link of the page 
 * @returns 
 */
function embedIframeCode(link)
{
	return '<iframe frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen width="100%" height="400"  src="'+link+'" ></iframe>'
}




/**
 * Movies Slider events
 */
jQuery(document).on('click', '.movie-slider', function(e) {
        
	const slider = document.querySelector('.movie-slider-ul');
	const items = document.querySelectorAll('.movie-slider');
	slider.append(items[0])

});
jQuery(document).on('click', '.movie-slider-arrow', function(e) {
	const slider = document.querySelector('.movie-slider-ul');
	const items = document.querySelectorAll('.movie-slider');
	jQuery(this).hasClass('next') ? slider.append(items[0]) : slider.prepend(items[items.length-1])
})
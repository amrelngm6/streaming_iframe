/**
 * Medians Video player
 * With Picture-in-picture
 * 
 */
$(function(){
	var myVideo;
	var playFrame;
	const processor = 
    {
		timerCallback(myVideo) {
			if (!playFrame || myVideo.ended )   {
				playFrame = null;
				return;
			}
			this.computeFrame(myVideo);
			setTimeout(() => {
				playFrame ? this.timerCallback(myVideo) : '';
			}, 16); 
		},

		doLoad(myVideo) {
			jQuery('#videoCanvasContainer').removeClass('hidden')
			playFrame = true;
			this.c1 = document.getElementById("videoCanvas");
			let canvasContainer = document.getElementById("videoCanvasContainer");
			
			this.ctx1 = this.c1.getContext("2d"); 

			let isDragging = false;
			let offsetX = 0;
			let offsetY = 0;

			canvasContainer.addEventListener('mousedown', function (e) {
				isDragging = true;
				videoCanvas.style.cursor = 'grabbing';

				// Calculate offset position to handle dragging smoothly
				offsetX = e.clientX - canvasContainer.getBoundingClientRect().left;
				offsetY = e.clientY - canvasContainer.getBoundingClientRect().top;
			});

			canvasContainer.addEventListener('ondragstart', function(){
				isDragging = true;
				videoCanvas.style.cursor = 'grabbing';
			}) 
			
			// Function to stop dragging
			window.addEventListener('mouseup', function () {
				isDragging = false;
				videoCanvas.style.cursor = 'grab';
			});
			
			// Function to drag the canvas
			window.addEventListener('mousemove', function (e) {
				if (isDragging) {
					// Calculate the new position
					const left = e.clientX - offsetX;
					const top = e.clientY - offsetY;

					// Update canvas position
					canvasContainer.style.left = `${left}px`;
					canvasContainer.style.top = `${top + 10}px`;

					// Set the position to absolute once dragging starts
					canvasContainer.style.position = 'fixed';
					canvasContainer.style.transform = 'none';
				}
			});

			this.width = 300;
			this.height =  200;
			myVideo.volume = getCookie('volume')
			this.timerCallback(myVideo)

		},

		computeFrame(myVideo) {
			this.ctx1.drawImage(myVideo, 0, 0, this.width, this.height);
			const frame = this.ctx1.getImageData(0, 0, this.width, this.height);
			const l = frame.data.length / 4;

			for (let i = 0; i < l; i++) {
			const grey =
				(frame.data[i * 4 + 0] +
				frame.data[i * 4 + 1] +
				frame.data[i * 4 + 2]) /
				3;
			}
			this.ctx1.putImageData(frame, 0, 0);

			return;
		},
	};
	
	/**
	 * Enable / Show video side picture-in-picture
	 */
	jQuery(document).on('click', '.video-side-popup', function(){
    	myVideo = document.getElementById("footer-video");
		if (myVideo.canPlayType("video/mp4")) {
			myVideo.setAttribute("src", jQuery(this).data('path'));
			processor.doLoad(myVideo);
			myVideo.play()
		}
	})

	jQuery(document).on('click', '#pause-video-side-popup', function(){
		playFrame = false;
    	myVideo = document.getElementById("footer-video");
		if (myVideo) {
			jQuery('#videoCanvasContainer').addClass('hidden')
			myVideo.pause()
		}
	})

	jQuery(document).on('click', '.video-side-popup', function(){
    	myVideo = document.getElementById("footer-video");
		if (myVideo.canPlayType("video/mp4")) {
			myVideo.setAttribute("src", jQuery(this).data('path'));
			processor.doLoad(myVideo);
			myVideo.play()
			
		}
	})

	
	
	
	
	jQuery(document).on('click', '.pause-video, .pause-channel', function(){
    	myVideo = document.getElementById(jQuery(this).attr('data-player')  );
		playVideo(myVideo)
	})
	jQuery(document).on('click', '.play-video, .play-channel', function(){
    	myVideo = document.getElementById(jQuery(this).attr('data-player')  );
		playVideo(myVideo)
	})

	/** On Play video */
	jQuery(document).on( "click", "video", function() {
		myVideo = document.getElementById(jQuery(this).attr('id') );
		dataContainer = jQuery(this).attr('data-container');
		if (dataContainer) {
			playVideo(myVideo)
		}
		
	});

	jQuery(document).on('change', '#video-volume, #channel-volume', function(){
    	myVideo = document.getElementById(jQuery(this).attr('data-player')  );
		myVideo.volume = jQuery(this).val()
		setCookie('volume', myVideo.volume, 7); // Set a cookie named 'username' with value 'john_doe' that expires in 7 days
	})
	jQuery(document).on('click', '.fullscreen', function(){
		videoContainer = document.getElementById(jQuery(this).attr('data-container'));
		return	(window.innerWidth == screen.width && window.innerHeight == screen.height) 
			? document.exitFullscreen()
			: videoContainer.requestFullscreen();
	})

	jQuery(document).on('dblclick', '#videoCanvas,video', function(){
		videoContainer = document.getElementById(jQuery(this).attr('data-container'));
		return	(window.innerWidth == screen.width && window.innerHeight == screen.height) 
			? document.exitFullscreen()
			: videoContainer.requestFullscreen();
	})


	/** On change current time */
	jQuery(document).on('click', 'progress', (e) => {
		let progress = document.getElementById('progress')
		const rect = progress.getBoundingClientRect();
		const pos = (e.pageX - rect.left) / progress.offsetWidth;
		myVideo = document.getElementById(e.target.dataset.player);
		myVideo.currentTime = pos * myVideo.duration;
		myVideo.play()
	});


})



/**
 * 
 * @param {*} myVideo Video player element
 * @param {*} autoPlay Auto-play video 
 * @returns void
 */
function playVideo(myVideo, autoPlay = true)
{	
	if (!myVideo)
		return;

	myVideo.volume = getCookie('volume')
	jQuery('#video-volume').val(getCookie('volume'))

	if (myVideo.paused && autoPlay) {
		myVideo.play()
		jQuery(`#${myVideo.dataset.container} .play-video`).hide().parent().find('.pause-video').fadeIn(200)
		jQuery('#channelContainer .play-channel').hide().parent().find('.pause-channel').fadeIn(200)
		jQuery('#video-overlay').fadeOut(200)
		jQuery('#channelContainer').css('z-index',  50)
	} else {
		jQuery(`#${myVideo.dataset.container} .pause-video`).hide().parent().find('.play-video').fadeIn(200)
		jQuery('#channelContainer .pause-channel').hide().parent().find('.play-channel').fadeIn(200)
		jQuery('#video-overlay').fadeIn(200)
		if (myVideo)
			myVideo.pause()
		
		jQuery('#channelContainer').css('z-index',  0)
	} 	
	jQuery('#video-duration-page').html(myVideo.duration > 0 ? convertToTime(myVideo.duration) : '--')
	jQuery('#videoContainer progress').attr("max", myVideo.duration);

	
	/** On time update */
	myVideo.addEventListener(
	"timeupdate",
	() => {
		jQuery(`#${myVideo.dataset.container} #current-time-page`).html(convertToTime(myVideo.currentTime));
		jQuery(`#${myVideo.dataset.container} progress`).val(myVideo.currentTime);
		jQuery(`#${myVideo.dataset.container} #video-duration-page`).html(myVideo.duration > 0 ? convertToTime(myVideo.duration) : '--')
	})
		
		
	/** On Load video */
	myVideo.addEventListener(
	"loadedmetadata",
	() => {
		jQuery(`#${myVideo.dataset.container} #current-time-page`).html(convertToTime(myVideo.currentTime));
		jQuery(`#${myVideo.dataset.container} #video-duration-page`).html(convertToTime(myVideo.duration))
		jQuery(`#${myVideo.dataset.container} progress`).attr("max", myVideo.duration);
		
		jQuery('#video-overlay').fadeOut(200)
		jQuery(`#${myVideo.dataset.container}`).css('z-index',  999)
	}, false );
		
	/** On Play video */
	myVideo.addEventListener(
	"play",
	() => {
		jQuery(`#${myVideo.dataset.container} #video-duration-page`).html(convertToTime(myVideo.duration))
		jQuery(`#${myVideo.dataset.container} progress`).attr("max", myVideo.duration);
		
		jQuery('#video-overlay').fadeOut(200)
		jQuery(`#${myVideo.dataset.container}`).css('z-index',  999)
	}, false);
		
	/** On Pause video */
	myVideo.addEventListener(
	"pause",
	() => {
		jQuery('#video-overlay').fadeIn(200)
		jQuery(`#${myVideo.dataset.container}`).css('z-index',  0)
	}, false );
}

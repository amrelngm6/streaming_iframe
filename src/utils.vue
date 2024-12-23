<script>
import { getCurrentInstance } from 'vue';
import moment from 'moment';
import axios from 'axios'
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export function today() {
  return moment().format('YYYY-MM-DD');
}
export function formatDate(date) {
  return moment(date).format('YYYY-MM-DD');
}

export function formatDateTime(date) {
  return moment(date).format('YYYY-MM-DD HH:mm a');
}

export function formatCustomTime(date, format = 'HH:mm a') {
  return moment(date).format(format);
}

export function toHHMMSS(val) {
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

// Function to get days between two dates
export function getDaysBetweenDates(to, from) {
    const toDate = new Date(to).getTime();
    const fromDate = new Date(from).getTime();
    const dateDiff = toDate - fromDate;

    return Math.round(dateDiff / (1000 * 60 * 60 * 24)); // Convert milliseconds to days
}

// Function to get percentage between two dates
export function getPercentageBetweenDates(to, from) {
    const toDate = new Date(to).getTime();
    const fromDate = new Date().getTime(); // Current date
    const dateDiff = toDate - fromDate;

    const currentDuration = Math.round(dateDiff / (1000 * 60 * 60 * 24)); // Convert milliseconds to days
    const totalDuration = getDaysBetweenDates(to, from);

    return Math.round(((totalDuration - currentDuration) / totalDuration) * 100);
}


export function durationMonthsDate(date, value) {
    
    return moment(date).add(value, 'months').format("YYYY-MM-DD");
}


export function remove(item, type) {
            
    if (!window.confirm(this.translate('confirm_delete')))
    {
        return null;
    }

    var params = new URLSearchParams();
    params.append('type', type)
    params.append('params[id]', item.id)
    handleRequest(params, '/api/delete').then(response => {
        this.$alert(response.result).then(() => {
            if (response && response.reload)
            {
                location.reload();
            }
        })
    })
}

export function customConfirm(message) {
  // Create a new promise
  return new Promise((resolve, reject) => {
    // Create modal elements
    const modal = document.createElement('div');
    modal.classList.add('delete-modal');
    modal.innerHTML = `
    <div class="swal2-container swal2-center swal2-backdrop-show overflow-y-auto"> 
        <div class="swal2-popup swal2-modal swal2-icon-warning swal2-show grid  py-10" >
            <div class="swal2-icon swal2-warning swal2-icon-show flex" >
                <div class="swal2-icon-content">!</div>
            </div>
            <div class="swal2-html-container  py-4" id="swal2-html-container" style="display: block;">${message}</div>
            <div class="swal2-actions" style="display: flex;">
                <button type="button" class=" text-white swal2-confirm btn fw-bold btn-danger" id="confirmBtn"
                    style="display: inline-block;">${translate('yes delete')}</button>
                <button type="button"
                    class="swal2-cancel btn fw-bold btn-active-light-primary" id="cancelBtn" style="display: inline-block;">${translate('no cancel')}</button>
            </div>
        </div>
    </div>
    `;

    document.body.appendChild(modal);

    // Add event listeners to buttons
    const confirmBtn = modal.querySelector('#confirmBtn');
    const cancelBtn = modal.querySelector('#cancelBtn');

    confirmBtn.addEventListener('click', () => {
      resolve(true); // Resolve the promise with true when confirmed
      document.body.removeChild(modal); // Remove modal from DOM
    });

    cancelBtn.addEventListener('click', () => {
      resolve(false); // Resolve the promise with false when canceled
      document.body.removeChild(modal); // Remove modal from DOM
    });
  });
}

export function deleteByKey(itemKey, item, type, title = 'Confirm delete') {
    
    
    customConfirm(translate(title))
    .then((result) => {
        if (result) {
            var params = new URLSearchParams();
            params.append('type', type)
            params.append('params['+itemKey+']', item[itemKey])
            handleRequest(params, '/api/delete').then(response => {
                showAlert(response.result);
                setTimeout(() => {
                    if (response && response.reload){
                        location.reload();
                    }
                }, 2000);

            })
        }
    });


}

export async function handleGetRequest(url) {
    
    let secondQuestionMarkIndex = url.indexOf('?', url.indexOf('?') + 1);
    if (secondQuestionMarkIndex !== -1) {
        url = url.slice(0, secondQuestionMarkIndex) + '&' + url.slice(secondQuestionMarkIndex + 1);
    }
    return await axios.get(url).then(response => {
        if (response.data.status) {
            return response.data.result ? response.data.result : response.data;
        } else {
            document.title = response.data.title ?? document.title;
            return response.data;
        }
    });
}

export async function handleRequest(params, url = '/api') {
    
    return await axios.post(url, params.toString()).then(response => {
        return response.data;
    });
}

export function translate(i) {

    // Access the current Vue instance
    const currentInstance = getCurrentInstance();
    // Access the root Vue instance
    if (!currentInstance || !i)
        return i;
    
    const rootVue = currentInstance.root.data;
    let key = i.toLowerCase().replaceAll(' ', '_');
    let k = i.replaceAll('_', ' ');
    let un_key = k.charAt(0).toUpperCase() + k.slice(1);

    let lang = rootVue.lang;
    return lang[key] ? lang[key] : un_key;
}

export function showAlert(response,duration = 3000)
{
    toast(response, {
        position: toast.POSITION.TOP_CENTER,
        autoClose: duration,
    });
}

export function setActiveStatus (item, key) 
{
    item[key] = !item[key];
}

export function checkAccess (auth) 
{
    if (auth && auth.role_id == 1)
        return true;

    if (!auth)
        return false;

    return true;
}

/**
 * Handle login access result 
 * 
 */
export function handleAccess (response)  
{
    
    const currentInstance = getCurrentInstance();
    if (currentInstance) 
        currentInstance.root.data.loader = false;

    if (response && (response.success && response.reload))
    {
        showAlert(response.result, 3500);
        setTimeout(() => {
            location.reload();
        }, 2000);

    } else if (response && (response.error || response.success) && response.result) {
        response ? showAlert(response.result, 3000) : null;
        if (response.result == 'Access limit exceeded')
        {
            showAlert(translate('Upgrade plan now'), 5000);
        }
    } else {
        response ? showAlert(response.error, 3000) : null;
    }

}

export async function findPlaces  (googleMapsApiKey, text, countries)
{
    let options = {
        componentRestrictions: { country: countries },
        apiKey: googleMapsApiKey,
        language: 'en',
    };
    const autocompleteService = new google.maps.places.AutocompleteService(options);
    return await autocompleteService.getPlacePredictions(
        { input: text },
        (predictions, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                // Assuming the first prediction is the most relevant one
                return predictions
            }
        }
    );

}  

export async function getPositionAddress  (lat, lng)  
{
    
    try {
        const place = await getPlaceIdFromPosition(lat, lng);
        return place.formatted_address;
    } catch (error) {
        console.error(error);
    }
}


/**
 * Get place information from Lat & Lng
 */
export async function getPlaceIdFromPosition  (lat, lng)  
{
    const geocoder = new google.maps.Geocoder();

    const latLng = { lat: parseFloat(lat), lng: parseFloat(lng) };

    return new Promise((resolve, reject) => {
        geocoder.geocode({ location: latLng }, (results, status) => {
        if (status === 'OK') {
            results[0] ? resolve(results[0]) : reject('No results found');
        } else {
            reject(`Geocoder failed due to: ${status}`);
        }
        });
    });
}


/**
 * Get Lat & Lng from Place_id
 */

export async function getPlaceDetails   (placeId)  
{
    
    const placesService = new google.maps.places.PlacesService(document.createElement('div'));

    return placesService.getDetails({ placeId }, (place, status) => 
    {
        if (status === google.maps.places.PlacesServiceStatus.OK && place) 
        {
            const location = place.geometry.location;
            return { lat: location.lat(), lng: location.lng() };
        } else {
            console.error('Failed to fetch place details');
        }
    });
}


export function sameRole (user, role)  
{
    if (user.role_id == role.role_id) 
    {
        return true
    }
    return false;
}

    
export function handleTabName (index) 
{
    return translate(index.replace('_', ' ').toUpperCase());
}

export function handleName (column) 
{
    return  (column && column.custom_field) ? 'params[field]['+column.key+']' : 'params['+column.key+']';
}

export function handleValue (column, item) 
{
    let subKeys = column.key.split(".")
    return  (column && column.custom_field) ?  (item.field ? item.field[column.key] : (item[subKeys[0]][subKeys[1]] ?? '')) : (item ? item[column.key] : null);
}


export function isInput (val) 
{
    switch (val) 
    {
        case 'color':
        case 'text':
        case 'number':
        case 'email':
        case 'time':
        case 'date':
        case 'phone':
        case 'number':
        case '':
            return true;
            break;
    }
    return false;
}


export function getProgressWidth  (requiredData = [], activeItem)  
{
    let progress = 0;
    
    let addVal = 100 / requiredData.length;

    for (let i = 0; i < requiredData.length; i++) 
    {
        if ( activeItem && activeItem.value)
        {
            if (activeItem.value[requiredData[i]])
                progress += addVal
    
            if (activeItem.value.position && activeItem.value.position[requiredData[i]])
                progress += addVal
        }
    }

    return progress;
}

export function handlePickup  (origin, destination, icon)  
{
    let data = {}
    data.icon = icon
    data.origin = { lat: parseFloat(origin.latitude), lng: parseFloat(origin.longitude) }
    data.destination = destination ? { lat: parseFloat(destination.latitude), lng: parseFloat(destination.longitude)} : data.origin;
    data.drag = false;
    return data;
}


export function decodePoly  (encodedString)  
{
      const polylinePoints = [];
      let index = 0;
      const len = encodedString.length;
      let lat = 0;
      let lng = 0;

      while (index < len) {
        let b;
        let shift = 0;
        let result = 0;

        do {
          b = encodedString.charCodeAt(index++) - 63;
          result |= (b & 0x1F) << shift;
          shift += 5;
        } while (b >= 0x20);

        const dlat = (result & 1) !== 0 ? ~(result >> 1) : result >> 1;
        lat += dlat;

        shift = 0;
        result = 0;

        do {
          b = encodedString.charCodeAt(index++) - 63;
          result |= (b & 0x1F) << shift;
          shift += 5;
        } while (b >= 0x20);

        const dlng = (result & 1) !== 0 ? ~(result >> 1) : result >> 1;
        lng += dlng;

        const latitude = lat / 1E5;
        const longitude = lng / 1E5;

        const position = { lat: latitude, lng: longitude };
        polylinePoints.push(position);
      }

      return polylinePoints;
}



</script>
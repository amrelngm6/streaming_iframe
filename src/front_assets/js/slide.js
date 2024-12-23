let currentIndexes = [0,0];

function showSlides() 
{
    const cont = document.querySelectorAll('.carousel-container')

    console.log(cont)
    
    if (!cont)
        return null;

    cont.forEach((container, index) => {
        container.setAttribute('data-index', index)
        showSlide(container, index)

    });

}

function showSlide(container, i)
{

    
    const id = container.getAttribute('id');
    const parentId = container.parentElement.getAttribute('id');

    const limit = parseInt(container.getAttribute('data-count') ? container.getAttribute('data-count') : 3);
    const slides = document.querySelectorAll('#'+id+' > div');
    // console.log(slides)
    const start = currentIndexes[i];
    const end = start + limit;
    slides.forEach((slide, index) => {
        if (index >= start && index < end) {
            slide.style.display = 'block';
            slide.classList.add('active')
        } else {
            slide.style.display = 'none';
            slide.classList.remove('active')
        }
    });

    listenButtons(parentId)
}

function listenButtons(parentId)
{
            
    const prevBtn = document.querySelector('#'+parentId+' .prev-btn');
    const nextBtn = document.querySelector('#'+parentId+' .next-btn');

    prevBtn.addEventListener('click', goToPrev);
    nextBtn.addEventListener('click', goToNext);
}

function goToNext(i) {
    
    const sliderId = i.target.parentElement.getAttribute('data-slider');

    const slides = document.querySelectorAll('#'+sliderId+' .slide');
    const cont = document.querySelector('#'+sliderId+'.carousel-container')
    const limit = parseInt(cont.getAttribute('data-count') ? cont.getAttribute('data-count') : 3);

    const currentIndex = cont.getAttribute('data-index');

    currentIndexes[currentIndex]++; 
    let indexVal =  currentIndexes[currentIndex]; 

    if (indexVal > slides.length || (indexVal + limit) > slides.length ) {
        currentIndexes[currentIndex] = 0
    }


    showSlide(cont, currentIndex);
}

function goToPrev(i) {
    const sliderId = i.target.parentElement.getAttribute('data-slider');

    const slides = document.querySelectorAll('#'+sliderId+' .slide');
    const cont = document.querySelector('#'+sliderId+'.carousel-container')
    const limit = parseInt(cont.getAttribute('data-count') ? cont.getAttribute('data-count') : 3);

    const currentIndex = cont.getAttribute('data-index');

    currentIndexes[currentIndex]--; 
    let index =  currentIndexes[currentIndex]; 
    if (index < 0) {
        currentIndexes[currentIndex] = slides.length - limit
    }
    showSlide(cont, currentIndex);
}


window.addEventListener('resize', function(e) {
    // Your JavaScript code to run on resize
    showSlides()
    const slides = document.querySelectorAll('.carousel-container');
    
    if (window.innerWidth < 600)
    {
        slides.forEach((slide, index) => {
            slide.setAttribute('data-count', 2)
        })
    } else if (window.innerWidth < 800) {

        slides.forEach((slide, index) => {
            slide.setAttribute('data-count', 3)
        })
    } else if (window.innerWidth < 1000) {

        slides.forEach((slide, index) => {
            slide.setAttribute('data-count', 4)
        })
    } else {

        slides.forEach((slide, index) => {
            const count = slide.getAttribute('data-count')
            slide.setAttribute('data-count', 5)
        })
    }
});



function handleSlides() 
{
  
// work in progress - needs some refactoring and will drop JQuery i promise :)
var instance = jQuery(".hs__wrapper");
$.each( instance, function(key, value) {
    
  var arrows = jQuery(instance[key]).find(".arrow"),
      prevArrow = arrows.filter('.arrow-prev'),
      nextArrow = arrows.filter('.arrow-next'),
      box = jQuery(instance[key]).find(".hs"), 
      count = jQuery(instance[key]).attr("data-count"), 
      x = 0,
      mx = 0,
      maxScrollWidth = box[0].scrollWidth - (box[0].clientWidth / 2) - (box.width() / 2);

      jQuery(arrows).on('click', function() {
      
    if (jQuery(this).hasClass("arrow-next")) {
      x = ((box.width() / (count > 1 ? 2 : 1))) + box.scrollLeft() - 10;
      box.animate({
        scrollLeft: x,
      })
    } else {
      x = ((box.width() / (count > 1 ? 2 : 1))) - box.scrollLeft() -10;
      box.animate({
        scrollLeft: -x,
      })
    }
      
  });
    
  jQuery(box).on({
    mousemove: function(e) {
      var mx2 = e.pageX - this.offsetLeft;
      if(mx) this.scrollLeft = this.sx + mx - mx2;
    },
    mousedown: function(e) {
      this.sx = this.scrollLeft;
      mx = e.pageX - this.offsetLeft;
    },
    scroll: function() {
      toggleArrows();
    }
  });

  jQuery(document).on("mouseup", function(){
    mx = 0;
  });
  
  function toggleArrows() {
    if(box.scrollLeft() > maxScrollWidth - 10) {
        // disable next button when right end has reached 
        nextArrow.addClass('disabled');
      } else if(box.scrollLeft() < 10) {
        // disable prev button when left end has reached 
        prevArrow.addClass('disabled')
      } else{
        // both are enabled
        nextArrow.removeClass('disabled');
        prevArrow.removeClass('disabled');
      }
  }
  
});


}
handleSlides()
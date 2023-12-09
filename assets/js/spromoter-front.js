document.addEventListener('DOMContentLoaded', function () {

  const reviewData = [
    { id: 1,
      date: 'Dec 9, 2023',
      avatar: 'https://picsum.photos/200',
      name: 'Bishwajit',
      ratings: '<i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star"></i>',
      comment: 'Love this plugin! Does exactly what it is supposed to do and so far without any real issues. (You might want to review some Dummy Text generation which contains words and even sentences with a meaning and that should not suppose to happen)'
    }, 
    { id: 2,
      date: 'Dec 10, 2023',
      avatar: 'https://picsum.photos/200',
      name: 'Bilash',
      ratings: '<i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star"></i> <i class="bi bi-star"></i>',
      comment: 'Well, seems like a nice plugin and all, but it doesnot work on mac.. Or atleast, I cant get it to work :( Tried to open the "Generate" by shortcut and by right-click - no such luck.. I wont be rating this plugin, since it dont work on Mac, so no worries -- wont destroy the ratings.. Atleast not untill I try the plugin lol :P'
    }, 
    { id: 3,
      date: 'Dec 11, 2023',
      avatar: 'https://picsum.photos/200',
      name: 'Nazrul',
      ratings: '<i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-half"></i> <i class="bi bi-star"></i>',
      comment: 'Useful plugin when youre putting templates together and alike. However only being able to replace text with latin as opposed to generating x amount of latin makes for a more complicated workflow in most cases. Generating specific amounts of dummy text = **** Integration with emmet = *****'
    },
    { id: 4,
      date: 'Dec 12, 2023',
      avatar: 'https://picsum.photos/200',
      name: 'Nothing',
      ratings: '<i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i>',
      comment: 'Its just awesome plugin to generate reviews. :)'
    }
  ];

  function createReviewContainers(reviewData) {
    const spromoterReviews = document.getElementById('spromoterReviews');

    reviewData.forEach((item, index) => {
      const reviewContainer = document.createElement('div');  
      reviewContainer.classList.add('spromoter-single-review');

      reviewContainer.innerHTML = `
      <div class="spromoter-comment-avatar">
        <img src="${item.avatar}" alt="${item.name}">
      </div>
      <div class="spromoter-comment-info">
        <div class="spromoter-name-ratings">
          <div>
            <div class="spromoter-name">${item.name}</div>
            <div class="spromoter-date">${item.date}</div>
          </div>
          <div class="spromoter-ratings">${item.ratings}</div>
        </div>
        <div class="spromoter-comments">${item.comment}</div>
      </div>`;

      spromoterReviews.appendChild(reviewContainer);
    });
  }
  
  createReviewContainers(reviewData);

  // Review Form
  const reviewForm = document.getElementById('spromoter-reviews-form');

    var reviewFormHTML = "" + 
    "<form class='spromoter-review-form' action='' method='post'>" +
      "<div class='spromoter-rating-wrap'>" +
        "<div class='spromoter-rating-text'>How was your experience with this product?</div>" +
        "<div class='spromoter-rating'>" +
          "<input type='radio' name='rating' id='ratingOne' checked />" +
          "<label for='ratingOne'><i class='bi bi-star-fill'></i></label>" +    
          "<input type='radio' name='rating' id='ratingTwo' />" +
          "<label for='ratingTwo'><i class='bi bi-star-fill'></i></label>" +    
          "<input type='radio' name='rating' id='ratingThree' />" +
          "<label for='ratingThree'><i class='bi bi-star-fill'></i></label>" +    
          "<input type='radio' name='rating' id='ratingFour' />" +
          "<label for='ratingFour'><i class='bi bi-star-fill'></i></label>" +    
          "<input type='radio' name='rating' id='ratingFive' />" +
          "<label for='ratingFive'><i class='bi bi-star-fill'></i></label>" +
        "</div>" +
      "</div>" +
      "<textarea name='' id='' class='spromoter-form-input' placeholder='Comment' required></textarea>" +
      "<input class='spromoter-form-file-input' type='file' id='formFile'>" +
      "<input type='text' id='' name='' class='spromoter-form-input' placeholder='Name' required>" +
      "<input type='email' id='' name='' class='spromoter-form-input' placeholder='Email' required>" +
      "<div class='spromoter-form-check'>" +
        "<input class='spromoter-form-check-input' type='checkbox' value='' id='flexCheckDefault'>" +
        "<label class='spromoter-form-check-label' for='flexCheckDefault'>Default checkbox</label>" +
      "</div>" +
      "<button type='submit' class='spromoter-button'>Submit</button>" +
    "</form>";

    reviewForm.innerHTML = reviewFormHTML;

    var singleReviewItems = document.querySelectorAll('.spromoter-single-review');

      singleReviewItems.forEach(function (item) {
        item.addEventListener('mouseover', function () {
          removeActiveClass();
          item.classList.add('active');
        });
      });

      function removeActiveClass() {
        singleReviewItems.forEach(function (item) {
          item.classList.remove('active');
        });
      }
});
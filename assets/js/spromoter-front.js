document.addEventListener('DOMContentLoaded', function () {

  // Showing Reviews
  const reviewContainer = document.querySelectorAll('.spromoter-single-review');

  let reviewsdata = ["", ]

  reviewContainer.forEach(function(element) {
      var author = element.getAttribute('data-spromoter-comment-author');
      var content = element.getAttribute('data-spromoter-comment-content');

      var reviewContent = "" +
      ""
      element.innerHTML = reviewContent;
  });

  // Review Form
  const reviewForm = document.getElementById('spromoter-reviews-form');

    var reviewFormHTML = "" + 
    "<form class='spromoter-review-form' action='' method='post'>" +
      "<input type='text' id='' name='' class='spromoter-form-input' placeholder='Name'>" +
      "<input type='email' id='' name='' class='spromoter-form-input' placeholder='Email'>" +
      "<fieldset class='spromoter-rating' id=''>" +
        "<input type='radio' id='star5' name='rating' value='5'>" +
        "<label class='full' for='star5' title='Awesome - 5 stars'></label>" +
        "<input type='radio' id='star4half' name='rating' value='4 and a half'>" +
        "<label class='half' for='star4half' title='Pretty good - 4.5 stars'></label>" +
        "<input type='radio' id='star4' name='rating' value='4'>" +
        "<label class='full' for='star4' title='Pretty good - 4 stars'></label>" +
        "<input type='radio' id='star3half' name='rating' value='3 and a half'>" +
        "<label class='half' for='star3half' title='Meh - 3.5 stars'></label>" +
        "<input type='radio' id='star3' name='rating' value='3'>" +
        "<label class='full' for='star3' title='Meh - 3 stars'></label>" +
        "<input type='radio' id='star2half' name='rating' value='2 and a half'>" +
        "<label class='half' for='star2half' title='Kinda bad - 2.5 stars'></label>" +
        "<input type='radio' id='star2' name='rating' value='2'>" +
        "<label class='half' for='star2' title='Kinda bad - 2 stars'></label>" +
        "<input type='radio' id='star1half' name='rating' value='1 and a half'>" +
        "<label class='half' for='star1half' title='Meh - 1.5 stars'></label>" +
        "<input type='radio' id='star1' name='rating' value='1'>" +
        "<label class='half' for='star1' title='Sucks big time - 1 star'></label>" +
        "<input type='radio' id='starhalf' name='rating' value='half'>" +
        "<label class='half' for='starhalf' title='Sucks big time - 0.5 stars'></label>" +
      "</fieldset>" +
      "<textarea name='' id='' class='spromoter-form-input' placeholder='Comment'></textarea>" +
      "<input class='spromoter-form-input' type='file' id='formFile'>" +
      "<div class='spromoter-form-check'>" +
        "<input class='spromoter-form-check-input' type='checkbox' value='' id='flexCheckDefault'>" +
        "<label class='spromoter-form-check-label' for='flexCheckDefault'>Default checkbox</label>" +
      "</div>" +
      "<button type='submit' class='spromoter-form-button'>Submit</button>" +
    "</form>";

    reviewForm.innerHTML = reviewFormHTML;
});
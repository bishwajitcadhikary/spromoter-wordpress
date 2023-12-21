class Spromoter {
    constructor() {
        this.baseUrl = 'http://spromoter.test/api/v1/';
        this.appId = document.querySelector('.spromoter-container') ?.dataset.spromoterAppId;
    }
    
    // Get reviews
    getReviews(){
        return fetch(this.baseUrl + `${this.appId}/reviews`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-App-Id': this.appId,
                }
            })
            .then(function (response) {
                if (!response.ok) {
                    return Promise.reject(response);
                }

                return response.json();
            })
    }

    // Submit review
    submitReview(data) {
        return fetch(this.baseUrl + `${this.appId}/reviews`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-App-Id': this.appId,
                },
                body: JSON.stringify(data)
            })
            .then(function (response) {
                if (!response.ok) {
                    return Promise.reject(response);
                }

                return response.json();
            })
    }
}

document.addEventListener('DOMContentLoaded', function () {
    let spromoter = new Spromoter();

    let spromoterContainer = document.querySelector('.spromoter-container');

    if (!spromoterContainer) {
        return;
    }

    let productId = spromoterContainer.dataset.spromoterProductId;
    let productTitle = spromoterContainer.dataset.spromoterProductTitle;
    let productImageUrl = spromoterContainer.dataset.spromoterProductImageUrl;
    let productUrl = spromoterContainer.dataset.spromoterProductUrl;
    let productDescription = spromoterContainer.dataset.spromoterProductDescription;
    let productLang = spromoterContainer.dataset.spromoterProductLang;

    // Get reviews from API
    let reviews = spromoter.getReviews();

    reviews.then(function (data) {
        let reviews = data.data?.reviews.map(function (review) {
            return createReviewData(review);
        })

        createReviewContainers(reviews);

        createBottomLine(data.data?.average_rating, data.data?.total_reviews);

        createLoadMoreButton(data.data?.has_more);
    });

    function createLoadMoreButton(has_more) {
        let loadMoreBtn = document.getElementById('spromoter-load-more-btn');

        if(has_more) {
            if (loadMoreBtn) {
                loadMoreBtn.style.display = "block";
            } else {
                let newLoadMoreBtn = document.createElement('button');
                newLoadMoreBtn.classList.add('spromoter-load-more-button');
                newLoadMoreBtn.id = 'spromoter-load-more-btn';
                newLoadMoreBtn.type = 'button';
                newLoadMoreBtn.innerText = 'Load more';
                document.getElementById('spromoterReviews').append(newLoadMoreBtn);

                newLoadMoreBtn.addEventListener('click', function() {
                    
                })
            }
        }
    }

    // Submit new review
    function createReviewContainers(reviewData) {
        reviewData.forEach((item, index) => {
            appendReview(item);
        });
    }

    function appendReview(item, isPrepend = false) {
        const spromoterReviews = document.getElementById('spromoterReviews');
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

        if (isPrepend) {
            spromoterReviews.prepend(reviewContainer);
        } else {
            spromoterReviews.appendChild(reviewContainer);
        }
    }

    function createReviewData(review) {
        let ratings = '';

        for (let i = 0; i < 5; i++) {
            if (i < review.rating) {
                ratings += '<i class="bi bi-star-fill"></i>';
            } else {
                ratings += '<i class="bi bi-star"></i>';
            }
        }

        return {
            id: review.id,
            date: review.created_at,
            avatar: review.avatar,
            name: review.name,
            ratings: ratings,
            comment: review.comment
        }
    }

    function createBottomLine(rating, totalReviews) {
        let bottomLine = document.querySelector('.spromoter-product-review-box');

        // Add rating
        let stars = '';
        for (let i = 0; i < 5; i++) {
            if (rating % 1 !== 0 && i === Math.floor(rating)) {
                stars += '<i class="bi bi-star-half"></i>';
            } else if (i < rating) {
                stars += '<i class="bi bi-star-fill"></i>';
            } else {
                stars += '<i class="bi bi-star"></i>';
            }
        }

        let bottomLineStars = document.createElement('div');
        bottomLineStars.classList.add('spromoter-product-review-box-rating');
        bottomLineStars.innerHTML = stars;
        bottomLine.appendChild(bottomLineStars);

        // Review summary
        const reviewAverage = document.getElementById('spromotertotalReviewsStars');
        reviewAverage.innerHTML = stars;

        document.getElementById('spromotertotalReviewsAverage').innerHTML = rating;
        document.getElementById('spromotertotalReviews').innerHTML = totalReviews;

        // Add write review button
        let writeReviewButton = document.createElement('div');
        writeReviewButton.classList.add('spromoter-write-review');
        writeReviewButton.innerText =  totalReviews > 0 ? totalReviews + ' Reviews' :'Write a review';
        //writeReviewButton.href = '#spromoterReviewContainer';
        bottomLine.appendChild(writeReviewButton);
    }

    // Create review form
    const reviewFormWrapper = document.getElementById('spromoter-reviews-form');

    let reviewFormWrapperHTML = "" +
        "<form class='spromoter-review-form'>" +
        "<div class='spromoter-rating-wrap'>" +
        "<div class='spromoter-rating-text'>How was your experience with this product?</div>" +
        "<div class='spromoter-rating'>" +
        "<input type='radio' name='spromoter_form_rating' id='spromoter_form_rating_one' value='1' checked />" +
        "<label for='spromoter_form_rating_one'><i class='bi bi-star-fill'></i></label>" +
        "<input type='radio' name='spromoter_form_rating' id='spromoter_form_rating_two' value='2' />" +
        "<label for='spromoter_form_rating_two'><i class='bi bi-star-fill'></i></label>" +
        "<input type='radio' name='spromoter_form_rating' id='spromoter_form_rating_three' value='3' />" +
        "<label for='spromoter_form_rating_three'><i class='bi bi-star-fill'></i></label>" +
        "<input type='radio' name='spromoter_form_rating' id='spromoter_form_rating_four' value='4' />" +
        "<label for='spromoter_form_rating_four'><i class='bi bi-star-fill'></i></label>" +
        "<input type='radio' name='spromoter_form_rating' id='spromoter_form_rating_five' value='5' />" +
        "<label for='spromoter_form_rating_five'><i class='bi bi-star-fill'></i></label>" +
        "</div>" +
        "</div>" +
        "<input type='text' name='spromoter_form_title' id='spromoter_form_title' class='spromoter-form-input' placeholder='Title' maxlength='255' required>" +
        "<textarea name='spromoter_form_comment' id='spromoter_form_comment' class='spromoter-form-input' placeholder='Comment' maxlength='500' required></textarea>" +
        // "<input class='spromoter-form-file-input' type='file' id='formFile'>" +
        "<input type='text' id='spromoter_form_name' name='spromoter_form_name' class='spromoter-form-input' placeholder='Name' maxlength='255' required>" +
        "<input type='email' id='spromoter_form_email' name='spromoter_form_email' class='spromoter-form-input' placeholder='Email' maxlength='255' required>" +
        "<button type='submit' class='spromoter-button'>Submit</button>" +
        "</form>";

    reviewFormWrapper.innerHTML = reviewFormWrapperHTML;

    // Create review filter
    const spromoterReviewFilter = document.getElementById('spromoterReviewFilter');

    let spromoterReviewFilterHTML = "" +
    '<h5 class="spromoter-review-filter-title">Search Reviews</h5>' +
    '<form class="spromoter-filter-form" id="" method="POST">' +
      '<div class="mb-3">' +
        '<label for="filter-search" class="spromoter-form-label mb-2">Search</label>' +
        '<input type="search" class="spromoter-form-input" id="filter-search" name="" placeholder="Search Reviews"/>' +
        '</div>' +
      '<div class="mb-3">' +
        '<label for="filter-ratings" class="spromoter-form-label mb-2">Ratings</label>' +
        '<select name="filter-ratings" id="filter-ratings" class="spromoter-form-input spromoter-form-select">' +
          '<option value="">5 star</option>' +
          '<option value="">4 star</option>' +
          '<option value="">3 star</option>' +
          '<option value="">2 star</option>' +
          '<option value="">1 star</option>' +
        '</select>' +
    '</div>' +
      '<div class="mb-3">' +
        '<label for="filter-review-created" class="spromoter-form-label mb-2">Date Published</label>' +
        '<select name="filter-review-created" id="filter-review-created" class="spromoter-form-input spromoter-form-select">' +
          '<option value="">Recently</option>' +
          '<option value="">Older</option>' +
        '</select>' +
      '</div>' +
    '</form>';

    spromoterReviewFilter.innerHTML = spromoterReviewFilterHTML;

    // Review form submit
    let reviewForm = document.querySelector('.spromoter-review-form');
    
    reviewForm.addEventListener('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(reviewForm);
        let rating = formData.get('spromoter_form_rating');
        let title = formData.get('spromoter_form_title');
        let comment = formData.get('spromoter_form_comment');
        let name = formData.get('spromoter_form_name');
        let email = formData.get('spromoter_form_email');

        let data = {
            product_id: productId,
            product_title: productTitle,
            product_image_url: productImageUrl,
            product_url: productUrl,
            product_description: productDescription,
            product_lang: productLang,
            rating: rating,
            title: title,
            comment: comment,
            name: name,
            email: email,
            collect_from: 'widget',
            source: 'woocommerce'
        }

        let submitReview = spromoter.submitReview(data);
        submitReview.then(function (data) {
            let review = createReviewData(data.data);

            // Prepend new review
            appendReview(review, true);

            // Reset form
            reviewForm.reset();

            // Hide form
            reviewForm.style.display = 'none';

            // Show success message
            let reviewButton = document.getElementById('spromoter-write-review-button');
            reviewButton.style.display = 'none';

            function reviewButtonHide() {
                reviewButton.style.display = 'block';
            }
            setTimeout(reviewButtonHide, 7000);

            let messageShowContainer = document.querySelector('.spromoter-total-review-show-wrap');
            let successMessage = document.createElement('div');
            successMessage.classList.add('spromoter-success-message');
            successMessage.innerHTML = '' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">' +
                    '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>' +
                '</svg>' +
                '<div class="success-message">Thank you for your review!</div>';
            messageShowContainer.appendChild(successMessage);

            function messageHide() {
                successMessage.style.display = 'none';
            }
            setTimeout(messageHide, 7000);
        });
    });

    // Open review form after click the 'write review button'
    document.getElementById('spromoter-write-review-button').addEventListener('click', function() {
        document.querySelector('.spromoter-review-form').style.display = "block";
    });

    // Scroll down to review section
    const productReviewBox = document.querySelector('div.spromoter-product-review-box');
    
    if (productReviewBox) {
        productReviewBox.addEventListener('click', function() {
        const spromoterWidgetTabLink = document.querySelector('li.spromoter_main_widget_tab > a');
    
        if (spromoterWidgetTabLink) {
            spromoterWidgetTabLink.click();

            const reviewContainerSection = document.getElementById('spromoterReviewContainer');
    
                if (reviewContainerSection) {
                    reviewContainerSection.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

});
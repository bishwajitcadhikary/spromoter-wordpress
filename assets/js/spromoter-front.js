class Spromoter {
    constructor() {
        this.baseUrl = 'https://spromoter.test/api/v1/';
        this.appId = document.querySelector('.spromoter-container')?.dataset.spromoterAppId;
    }

    // Get reviews
    getReviews() {
        return fetch(this.baseUrl + `${this.appId}/reviews`, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-App-Id': this.appId,
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    console.log(response);

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
                    console.log(response);

                    return Promise.reject(response);
                }

                return response.json();
            })
    }
}

document.addEventListener('DOMContentLoaded', function () {
    let spromoter = new Spromoter();

    let spromoterContainer = document.querySelector('.spromoter-container')

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
    let reviews = spromoter.getReviews()
    reviews.then(function (data) {
        let reviews = data.data?.reviews.map(function (review) {
            return createReviewData(review)
        });

        createReviewContainers(reviews);
    });

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
        }else{
            spromoterReviews.appendChild(reviewContainer);
        }
    }

    function createReviewData(review){
        let ratings = '';

        for (let i = 0; i < review.rating; i++) {
            ratings += '<i class="bi bi-star-fill"></i>';
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

    // Review Form
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

    // Review Form Submit
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

            // Remove form
            reviewFormWrapper.innerHTML = "<div class='spromoter-success-message'>Thank you for your review!</div>";
        });
    });

    let singleReviewItems = document.querySelectorAll('.spromoter-single-review');

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
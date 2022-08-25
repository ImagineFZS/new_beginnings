<template>
    <div>

        <div v-if="loading">Loading...</div>
        <div v-else>

        <div v-if="alreadyReviewed">
            <h3>Review Exists</h3>
        </div>
        <div v-else>

            <div class="form-group">
            <label class="text-muted">Select Rating</label>
            <star-rating :rating="review.rating" class="fa-3x" v-on:rating:changed="review.rating = $event"></star-rating>
            </div>
            <div class="form-group">
                <label for="content" class="text-muted">Description</label>
                <textarea name="content" cols="30" rows="10" class="form-control" v-model="review.content"></textarea>
            </div>

            <br>
            <button style="width: 100%;" class="btn btn-lg btn-primary btn-block">Submit</button>

        </div>

        </div>

    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            review: {
                rating: 5,
                content: null
            },
            existingReview: null,
            loading: false
        }
    },
    methods: {
        onRatingChanged(rating)
        {
            console.log(rating);
        }
    },
    created(){
        this.loading =  true;

        axios.get(`/api/reviews/${this.$route.params.id}`).then(response => (this.existingReview = response.data.data))
        .catch(error => {

        }).then(() => this.loading = false);

    },
    computed: {
        alreadyReviewed(){
            if(this.existingReview != null)
            {
                return true;

            }else
            {
                return false;
            }
        }
    }
}
</script>


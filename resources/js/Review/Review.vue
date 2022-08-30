<template>
    <div>

        <success v-if="success">
            The operation was successful.
        </success>

        <fatal-error v-if="error"></fatal-error>

        <div class="row" v-if="!success && !error">

        <div :class="[{'col-md-4': twoColumns}, {'d-none': oneColumn}]">
            <div class="card">
                 <div class="card-body">
                    <div v-if="loading">Loading...</div>
                    <div v-if="hasBooking">
                        <p>Stayed at <router-link :to="{ name: 'Bookable', params: { id: booking.bookable.bookable_id } }">{{booking.bookable.title}}</router-link></p>
                        <p>From {{ booking.from }} to {{ booking.to }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div :class="[{'col-md-8': twoColumns}, {'col-md-12': oneColumn}]">

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
                    <textarea name="content"
                    cols="30"
                    rows="10"
                    class="form-control"
                    v-model="review.content"
                    :class="[{'is-invalid': errorFor('content')}]">
                    </textarea>

                    <v-errors :errors="errorFor('content')"></v-errors>

                </div>

                <br>
                <button style="width: 100%;" class="btn btn-lg btn-primary btn-block" @click.prevent="submit" :disabled="sending">Submit</button>

            </div>
            </div>

        </div>

        </div>

    </div>
</template>

<script>

import { is404, is422 } from "./../shared/utils/response";
import axios from 'axios';
import FatalError from '../shared/components/FatalError.vue';
import validationErrors from "./../shared/mixins/validateErrors";

export default {
    mixins: [validationErrors],
    components: { FatalError },
    data() {
        return {
            review: {
                id: null,
                rating: 5,
                content: null
            },
            existingReview: null,
            loading: false,
            booking: null,
            error: false,
            sending: false,
            success: false
        };
    },
    methods: {
        onRatingChanged(rating) {
            console.log(rating);
        }
    },
    async created() {
        this.review.id = this.$route.params.id;
        this.loading = true;

        try{
            this.existingReview = await (await axios.get(`/api/reviews/${this.review.id}`)).data.data;

        }catch(err){

            if(is404(err)){

                try{

                    this.booking = (await axios.get(`/api/booking-by-review/${this.review.id}`)).data.data;

                }catch(err){

                    is404(err) ? {} : (this.error = true);
                }

            }else{

                this.error = true;
            }
        }

        this.loading = false;
    },
    computed: {
        alreadyReviewed() {
            return this.hasReview || !this.booking;
        },
        hasReview() {
            if (this.existingReview != null) {
                return true;
            }
            else {
                return false;
            }
        },
        hasBooking() {
            if (this.booking != null) {
                return true;
            }
            else {
                return false;
            }
        },
        oneColumn() {
            return !this.loading && this.alreadyReviewed;
        },
        twoColumns() {
            return this.loading || !this.alreadyReviewed;
        }
    },
    methods: {
        submit() {

            this.errors = null;
            this.sending = true;
            this.success = false;

            axios.post(`/api/reviews`, this.review)
                .then(response => {
                    if(response.status == 201)
                    {
                        this.success = true;
                    }
                })
                .catch(err => {
                    if(is422(err))
                    {
                        const errors = err.response.data.errors;

                        if(errors['content'] && (_.size(errors) == 1))
                        {
                            this.errors = errors;
                             return;
                        }
                    }

                    this.error = true;
                })
                .then(() => (this.sending = false));
        }
    },
}
</script>
